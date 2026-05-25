<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\MonthlyReport;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonthlyReportService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl;

    // Mapping mood angka 1-5 → label Indonesia
    private array $moodLabels = [
        1 => 'Sangat Sedih',
        2 => 'Sedih',
        3 => 'Biasa',
        4 => 'Senang',
        5 => 'Sangat Senang',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model  = config('services.gemini.model', 'gemini-2.5-flash');

        // pakai v1beta untuk kompatibilitas model terbaru
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    // ─── Generate monthly report untuk 1 murid ───────────────────────────────

    public function generate(int $studentId, int $month, int $year): MonthlyReport
    {
        $student = Student::findOrFail($studentId);

        $reports = DailyReport::with(['detail', 'classification'])
            ->where('student_id', $studentId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        if ($reports->isEmpty()) {
            throw new \Exception(
                "Tidak ada laporan untuk murid {$student->name} bulan {$month}/{$year}."
            );
        }

        $stats    = $this->calculateStats($reports);
        $aiOutput = $this->generateAiOutput(
            $student,
            $stats,
            $reports,
            $month,
            $year
        );

        $monthlyReport = MonthlyReport::updateOrCreate(
            [
                'student_id' => $studentId,
                'month'      => $month,
                'year'       => $year,
            ],
            array_merge($stats, [
                'ai_summary'        => $aiOutput['summary'],
                'ai_attention'      => $aiOutput['attention'],
                'ai_recommendation' => $aiOutput['recommendation'],
                'status'            => 'generated',
                'generated_at'      => now(),
            ])
        );

        return $monthlyReport;
    }

    // ─── Hitung statistik ─────────────────────────────────────────────────────

    private function calculateStats($reports): array
    {
        $classifications = $reports->pluck('classification')->filter();
        $details         = $reports->pluck('detail')->filter();
        $total           = $reports->count();

        // Helper: hitung persentase
        $calcPercent = function ($items, string $field) use ($total): array {

            return $items->groupBy($field)
                ->filter(fn($g, $key) => $key !== '' && $key !== null)
                ->map(fn($g) => [
                    'count'   => $g->count(),
                    'percent' => round(($g->count() / $total) * 100, 1),
                ])
                ->sortByDesc('count')
                ->toArray();
        };

        // Helper: hitung frekuensi text comma separated
        $calcTextFrequency = function ($items, string $field) use ($total): array {

            $counts = [];

            foreach ($items as $item) {

                $raw = trim($item->{$field} ?? '');

                if (empty($raw)) {
                    continue;
                }

                $values = array_map('trim', explode(',', $raw));

                foreach (array_filter($values) as $val) {

                    $key = strtolower($val);

                    if (!isset($counts[$key])) {
                        $counts[$key] = [
                            'label' => ucfirst($val),
                            'count' => 0,
                        ];
                    }

                    $counts[$key]['count']++;
                }
            }

            usort($counts, fn($a, $b) => $b['count'] <=> $a['count']);

            $result = [];

            foreach ($counts as $item) {
                $result[$item['label']] = [
                    'count'   => $item['count'],
                    'percent' => round(($item['count'] / $total) * 100, 1),
                ];
            }

            return $result;
        };

        // ── Mood ──────────────────────────────────────────────────────────────

        $moodArrivalCounts = [];
        $moodEndCounts     = [];

        foreach ($details as $d) {

            $arrivalLabel = $this->moodLabels[$d->mood_arrival] ?? null;
            $endLabel     = $this->moodLabels[$d->mood_end] ?? null;

            if ($arrivalLabel) {
                $moodArrivalCounts[$arrivalLabel] =
                    ($moodArrivalCounts[$arrivalLabel] ?? 0) + 1;
            }

            if ($endLabel) {
                $moodEndCounts[$endLabel] =
                    ($moodEndCounts[$endLabel] ?? 0) + 1;
            }
        }

        $formatMoodCounts = function (array $counts) use ($total): array {

            arsort($counts);

            return array_map(fn($c) => [
                'count'   => $c,
                'percent' => round(($c / $total) * 100, 1),
            ], $counts);
        };

        // mood positif ≥ 4
        $moodPositiveCount = $details
            ->filter(fn($d) => ($d->mood_arrival ?? 0) >= 4)
            ->count();

        $moodPositiveStats = [
            'positive' => [
                'count'   => $moodPositiveCount,
                'percent' => $total > 0
                    ? round(($moodPositiveCount / $total) * 100, 1)
                    : 0,
            ],
            'neutral_negative' => [
                'count'   => $total - $moodPositiveCount,
                'percent' => $total > 0
                    ? round((($total - $moodPositiveCount) / $total) * 100, 1)
                    : 0,
            ],
        ];

        return [

            // ── Dasar ────────────────────────────────────────────────────────

            'total_reports'          => $total,
            'total_homework_days'    => $details->where('has_homework', true)->count(),
            'total_no_homework_days' => $details->where('has_homework', false)->count(),
            'total_challenges'       => $classifications->where('has_challenge', true)->count(),

            // ── Kondisi Fisik ────────────────────────────────────────────────

            // kondisi saat datang
            'physical_condition_stats' =>
                $calcPercent($details, 'physical_condition_arrival'),

            // kondisi saat pulang
            'physical_condition_end_stats' =>
                $calcPercent($details, 'physical_condition_end'),

            // ── Mood ─────────────────────────────────────────────────────────

            'mood_arrival_avg'      => round($details->avg('mood_arrival'), 2),
            'mood_end_avg'          => round($details->avg('mood_end'), 2),
            'mood_arrival_dominant' => $formatMoodCounts($moodArrivalCounts),
            'mood_end_dominant'     => $formatMoodCounts($moodEndCounts),
            'mood_positive_stats'   => $moodPositiveStats,
            'mood_trend_stats'      => $calcPercent($classifications, 'mood_trend'),

            // ── Perilaku ────────────────────────────────────────────────────

            'behavior_stats' =>
                $calcPercent($details, 'behavior'),

            // ── Respon ──────────────────────────────────────────────────────

            'response_stats' =>
                $calcPercent($details, 'response'),

            // ── Kendala ─────────────────────────────────────────────────────

            'challenge_stats' =>
                $calcPercent($details, 'challenge'),

            // ── Aktivitas ───────────────────────────────────────────────────

            'activity_stats' =>
                $calcTextFrequency($details, 'activity_notes'),

            // ── Solusi ──────────────────────────────────────────────────────

            'solution_stats' =>
                $calcTextFrequency($details, 'solution_notes'),

            // ── Overall ─────────────────────────────────────────────────────

            'overall_score_stats' =>
                $calcPercent($classifications, 'overall_score'),

            // ── Tidak ada di DB ─────────────────────────────────────────────

            'independence_stats' => null,
        ];
    }

    // ─── Generate AI output ──────────────────────────────────────────────────

    private function generateAiOutput(
        $student,
        array $stats,
        $reports,
        int $month,
        int $year
    ): array {

        $bulanIndo = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // catatan other
        $otherNotes = $reports->map(function ($r) {

            $d = $r->detail;

            if (!$d) {
                return null;
            }

            $notes = array_filter([

                // kondisi datang
                $d->physical_condition_other ?? null,

                // kondisi pulang
                $d->physical_condition_end_other ?? null,

                $d->behavior_other ?? null,
                $d->response_other ?? null,
                $d->challenge_other ?? null,
            ]);

            return empty($notes)
                ? null
                : implode('; ', $notes);

        })->filter()->implode(' | ');

        $dominantBehavior  = $this->getDominant($stats['behavior_stats']);
        $dominantChallenge = $this->getDominant($stats['challenge_stats']);
        $dominantOverall   = $this->getDominant($stats['overall_score_stats']);
        $dominantMoodTrend = $this->getDominant($stats['mood_trend_stats']);
        $dominantMoodArr   = $this->getDominant($stats['mood_arrival_dominant']);
        $dominantMoodEnd   = $this->getDominant($stats['mood_end_dominant']);

        $topActivities = implode(
            ', ',
            array_slice(array_keys($stats['activity_stats']), 0, 4)
        );

        $topSolutions = implode(
            ', ',
            array_slice(array_keys($stats['solution_stats']), 0, 3)
        );

        $moodPositivePct =
            $stats['mood_positive_stats']['positive']['percent'] ?? 0;

        $prompt = "Kamu adalah asisten laporan Sekolah Berkebutuhan Khusus Lentera Fajar.\n"
            . "Tulis laporan bulanan untuk orang tua dengan nada hangat dan mudah dipahami.\n"
            . "WAJIB ikuti format di bawah persis — jangan tambah teks lain di luar format.\n\n"

            . "Data {$student->name}, {$bulanIndo[$month]} {$year}:\n"

            . "- Total kehadiran: {$stats['total_reports']} hari\n"
            . "- Hari ada PR: {$stats['total_homework_days']} hari\n"
            . "- Hari ada kendala: {$stats['total_challenges']} hari\n"

            . "- Mood positif keseluruhan: {$moodPositivePct}%\n"

            . "- Mood saat datang: {$stats['mood_arrival_avg']}/5 "
            . "(dominan: {$dominantMoodArr})\n"

            . "- Mood saat pulang: {$stats['mood_end_avg']}/5 "
            . "(dominan: {$dominantMoodEnd})\n"

            . "- Tren mood dominan: {$dominantMoodTrend}\n"

            . "- Perilaku dominan: {$dominantBehavior}\n"

            . "- Kendala dominan: {$dominantChallenge}\n"

            . "- Kegiatan yang sering diikuti: "
            . ($topActivities ?: 'tidak ada data') . "\n"

            . "- Solusi yang sering diterapkan: "
            . ($topSolutions ?: 'tidak ada data') . "\n"

            . "- Skor umum dominan: {$dominantOverall}\n"

            . "- Catatan guru: "
            . ($otherNotes ?: 'tidak ada catatan tambahan') . "\n\n"

            . "FORMAT OUTPUT (ikuti persis):\n"

            . "RINGKASAN:\n"
            . "[3 kalimat tentang perkembangan anak bulan ini]\n\n"

            . "PERHATIAN:\n"
            . "[2 kalimat hal yang perlu diperhatikan orang tua]\n\n"

            . "REKOMENDASI:\n"
            . "[2 kalimat saran untuk bulan depan]";

        $requestBody = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature'     => 0.4,
                'maxOutputTokens' => 3000,
            ],
        ];

        try {

            Log::info("Generate AI report untuk {$student->name}");

            $response = $this->callGemini($requestBody);

            // retry kalau kena rate limit
            if ($response->status() === 429) {

                $retryAfter = $response->json(
                    'error.details.2.retryDelay',
                    '60s'
                );

                $seconds = (int) filter_var(
                    $retryAfter,
                    FILTER_SANITIZE_NUMBER_INT
                );

                $waitTime = max($seconds + 5, 60);

                Log::warning(
                    "Rate limit Gemini. Retry {$waitTime} detik..."
                );

                sleep($waitTime);

                $response = $this->callGemini($requestBody);
            }

            if ($response->failed()) {

                throw new \Exception(
                    "Gemini gagal. Status: {$response->status()} "
                    . "Body: {$response->body()}"
                );
            }

            $text = $response->json(
                'candidates.0.content.parts.0.text',
                ''
            );

            if (empty(trim($text))) {
                throw new \Exception("Teks kosong dari Gemini.");
            }

            return $this->parseAiOutput($text);

        } catch (\Exception $e) {

            Log::error(
                "Gemini error untuk {$student->name}: "
                . $e->getMessage()
            );

            return [
                'summary'        => null,
                'attention'      => null,
                'recommendation' => null,
            ];
        }
    }

    // ─── Call Gemini ─────────────────────────────────────────────────────────

    private function callGemini(array $body)
    {
        return Http::withoutVerifying()
            ->timeout(90)
            ->connectTimeout(30)
            ->post("{$this->apiUrl}?key={$this->apiKey}", $body);
    }

    // ─── Parse output AI ─────────────────────────────────────────────────────

    private function parseAiOutput(string $text): array
    {
        $summary        = '';
        $attention      = '';
        $recommendation = '';

        if (
            preg_match(
                '/RINGKASAN:\s*(.*?)(?=PERHATIAN:|$)/si',
                $text,
                $m
            )
        ) {
            $summary = trim($m[1]);
        }

        if (
            preg_match(
                '/PERHATIAN:\s*(.*?)(?=REKOMENDASI:|$)/si',
                $text,
                $m
            )
        ) {
            $attention = trim($m[1]);
        }

        if (
            preg_match(
                '/REKOMENDASI:\s*(.*?)$/si',
                $text,
                $m
            )
        ) {
            $recommendation = trim($m[1]);
        }

        // fallback kalau format AI kacau
        if (
            empty($summary) &&
            empty($attention) &&
            empty($recommendation)
        ) {

            Log::warning(
                "Parse gagal, simpan raw text."
            );

            return [
                'summary'        => $text,
                'attention'      => null,
                'recommendation' => null,
            ];
        }

        return [
            'summary'        => $summary ?: null,
            'attention'      => $attention ?: null,
            'recommendation' => $recommendation ?: null,
        ];
    }

    // ─── Helper dominant ─────────────────────────────────────────────────────

    private function getDominant(array $stats): string
    {
        if (empty($stats)) {
            return '-';
        }

        $key = array_key_first($stats);

        $pct = is_array($stats[$key])
            ? ($stats[$key]['percent'] ?? 0)
            : $stats[$key];

        return "{$key} ({$pct}%)";
    }

    // ─── Generate semua murid ────────────────────────────────────────────────

    public function generateForAllStudents(
        int $month,
        int $year
    ): array {

        $results = [];

        $studentIds = DailyReport::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->distinct()
            ->pluck('student_id');

        Log::info(
            "Generate monthly report {$month}/{$year}"
        );

        foreach ($studentIds as $index => $studentId) {

            // delay antar request Gemini
            if ($index > 0) {

                Log::info(
                    "Delay 15 detik sebelum murid ke-"
                    . ($index + 1)
                );

                sleep(15);
            }

            try {

                $report = $this->generate(
                    $studentId,
                    $month,
                    $year
                );

                $results[] = [
                    'student_id' => $studentId,
                    'status'     => 'success',
                    'report_id'  => $report->id,
                ];

                Log::info(
                    "Sukses generate student_id {$studentId}"
                );

            } catch (\Exception $e) {

                $results[] = [
                    'student_id' => $studentId,
                    'status'     => 'failed',
                    'error'      => $e->getMessage(),
                ];

                Log::error(
                    "Gagal student_id {$studentId}: "
                    . $e->getMessage()
                );
            }
        }

        return $results;
    }
}
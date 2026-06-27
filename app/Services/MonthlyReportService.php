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

    private array $moodLabels = [
        1 => 'Sangat Sedih', 2 => 'Sedih', 3 => 'Biasa',
        4 => 'Senang', 5 => 'Sangat Senang',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model  = config('services.gemini.model', 'gemini-2.5-flash');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1/models/{$this->model}:generateContent";
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
            throw new \Exception("Tidak ada laporan untuk murid {$student->name} bulan {$month}/{$year}.");
        }

        $stats    = $this->calculateStats($reports);
        $aiOutput = $this->generateAiOutput($student, $stats, $reports, $month, $year);

        $monthlyReport = MonthlyReport::updateOrCreate(
            ['student_id' => $studentId, 'month' => $month, 'year' => $year],
            array_merge($stats, [
                'ai_summary'          => $aiOutput['summary'],
                'ai_attention'        => $aiOutput['attention'],
                'ai_recommendation'   => $aiOutput['recommendation'],
                'ai_headline'         => $aiOutput['headline'],
                'ai_headline_emoji'   => $aiOutput['headline_emoji'],
                'ai_attention_trend'  => $aiOutput['attention_trend'],
                'ai_attention_note'   => $aiOutput['attention_note'],
                'status'              => 'generated',
                'generated_at'        => now(),
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

        $calcTextFrequency = function ($items, string $field) use ($total): array {
            $counts = [];
            foreach ($items as $item) {
                $raw = trim($item->{$field} ?? '');
                if (empty($raw)) continue;
                $values = array_map('trim', explode(',', $raw));
                foreach (array_filter($values) as $val) {
                    $key = strtolower($val);
                    if (!isset($counts[$key])) {
                        $counts[$key] = ['label' => ucfirst($val), 'count' => 0];
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
            if ($arrivalLabel) $moodArrivalCounts[$arrivalLabel] = ($moodArrivalCounts[$arrivalLabel] ?? 0) + 1;
            if ($endLabel)     $moodEndCounts[$endLabel]         = ($moodEndCounts[$endLabel] ?? 0) + 1;
        }

        $formatMoodCounts = function (array $counts) use ($total): array {
            arsort($counts);
            return array_map(fn($c) => [
                'count'   => $c,
                'percent' => round(($c / $total) * 100, 1),
            ], $counts);
        };

        $moodPositiveCount = $details->filter(fn($d) => ($d->mood_arrival ?? 0) >= 4)->count();
        $moodPositiveStats = [
            'positive' => [
                'count'   => $moodPositiveCount,
                'percent' => $total > 0 ? round(($moodPositiveCount / $total) * 100, 1) : 0,
            ],
            'neutral_negative' => [
                'count'   => $total - $moodPositiveCount,
                'percent' => $total > 0 ? round((($total - $moodPositiveCount) / $total) * 100, 1) : 0,
            ],
        ];

        // ── Attendance stats ──────────────────────────────────────────────────
        $attendanceStats = $reports->groupBy('attendance_status')
            ->filter(fn($g, $key) => $key !== '' && $key !== null)
            ->map(fn($g) => [
                'count'   => $g->count(),
                'percent' => round(($g->count() / $total) * 100, 1),
            ])
            ->toArray();

        return [
            'total_reports'                  => $total,
            'total_homework_days'            => $details->where('has_homework', true)->count(),
            'total_no_homework_days'         => $details->where('has_homework', false)->count(),
            'total_challenges'               => $classifications->where('has_challenge', true)->count(),
            'attendance_stats'               => $attendanceStats,
            'physical_condition_stats'       => $calcPercent($details, 'physical_condition_arrival'),
            'physical_condition_end_stats'   => $calcPercent($details, 'physical_condition_end'),
            'physical_energy_arrival_stats'  => $calcPercent($details, 'physical_energy_arrival'),
            'physical_energy_end_stats'      => $calcPercent($details, 'physical_energy_end'),
            'mood_arrival_avg'               => round($details->avg('mood_arrival'), 2),
            'mood_end_avg'                   => round($details->avg('mood_end'), 2),
            'mood_arrival_dominant'          => $formatMoodCounts($moodArrivalCounts),
            'mood_end_dominant'              => $formatMoodCounts($moodEndCounts),
            'mood_positive_stats'            => $moodPositiveStats,
            'mood_trend_stats'               => $calcPercent($classifications, 'mood_trend'),
            'behavior_stats'                 => $calcPercent($details, 'behavior'),
            'response_stats'                 => $calcPercent($details, 'response'),
            'independence_stats'             => $calcPercent($details, 'independence'),
            'challenge_stats'                => $calcPercent($details, 'challenge'),
            'activity_stats'                 => $calcTextFrequency($details, 'activity_notes'),
            'solution_stats'                 => $calcTextFrequency($details, 'solution_notes'),
            'overall_score_stats'            => $calcPercent($classifications, 'overall_score'),
            'achievement_tag_stats'          => $calcPercent($details, 'achievement_tag'),
            'communication_mode_stats'       => $calcPercent($details, 'communication_mode'),
            'communication_initiative_stats' => $calcPercent($details, 'communication_initiative'),
            'social_with_teacher_stats'      => $calcPercent($details, 'social_with_teacher'),
            'social_with_peers_stats'        => $calcPercent($details, 'social_with_peers'),
        ];
    }

    // ─── Generate AI output ───────────────────────────────────────────────────

    private function generateAiOutput($student, array $stats, $reports, int $month, int $year): array
    {
        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $otherNotes = $reports->map(function ($r) {
            $d = $r->detail;
            if (!$d) return null;
            $notes = array_filter([
                $d->physical_condition_other ?? null,
                $d->physical_condition_end_other ?? null,
                $d->physical_energy_arrival_other ?? null,
                $d->physical_energy_end_other ?? null,
                $d->independence_other ?? null,
                $d->behavior_other ?? null,
                $d->response_other ?? null,
                $d->challenge_other ?? null,
                $d->achievement_note ?? null,
            ]);
            return empty($notes) ? null : implode('; ', $notes);
        })->filter()->implode(' | ');

        $dominantBehavior       = $this->getDominant($stats['behavior_stats']);
        $dominantChallenge      = $this->getDominant($stats['challenge_stats']);
        $dominantOverall        = $this->getDominant($stats['overall_score_stats']);
        $dominantMoodTrend      = $this->getDominant($stats['mood_trend_stats']);
        $dominantMoodArr        = $this->getDominant($stats['mood_arrival_dominant']);
        $dominantMoodEnd        = $this->getDominant($stats['mood_end_dominant']);
        $dominantEnergyArr      = $this->getDominant($stats['physical_energy_arrival_stats']);
        $dominantEnergyEnd      = $this->getDominant($stats['physical_energy_end_stats']);
        $dominantIndependence   = $this->getDominant($stats['independence_stats']);
        $dominantCommMode       = $this->getDominant($stats['communication_mode_stats']);
        $dominantCommInitiative = $this->getDominant($stats['communication_initiative_stats']);
        $dominantSocialTeacher  = $this->getDominant($stats['social_with_teacher_stats']);
        $dominantSocialPeers    = $this->getDominant($stats['social_with_peers_stats']);
        $dominantAchievement    = $this->getDominant($stats['achievement_tag_stats']);

        $topActivities = implode(', ', array_slice(array_keys($stats['activity_stats']), 0, 4));
        $topSolutions  = implode(', ', array_slice(array_keys($stats['solution_stats']), 0, 3));

        $moodPositivePct = $stats['mood_positive_stats']['positive']['percent'] ?? 0;
        $hadirCount      = $stats['attendance_stats']['hadir']['count'] ?? $stats['total_reports'];
        $sakitCount      = $stats['attendance_stats']['sakit']['count'] ?? 0;
        $izinCount       = $stats['attendance_stats']['izin']['count']  ?? 0;
        $alphaCount      = $stats['attendance_stats']['alpha']['count'] ?? 0;

        $firstName = explode(' ', $student->name)[0];

        $prompt = "Kamu adalah asisten laporan Sekolah Berkebutuhan Khusus Lentera Fajar.\n"
            . "Tulis laporan bulanan untuk orang tua dengan nada hangat dan mudah dipahami.\n"
            . "WAJIB ikuti format di bawah persis — jangan tambah teks lain di luar format.\n\n"

            . "Data {$student->name}, {$bulanIndo[$month]} {$year}:\n"
            . "- Total laporan: {$stats['total_reports']} hari\n"
            . "- Kehadiran: hadir {$hadirCount}, sakit {$sakitCount}, izin {$izinCount}, alpha {$alphaCount}\n"
            . "- Hari ada PR: {$stats['total_homework_days']} hari\n"
            . "- Hari ada kendala: {$stats['total_challenges']} hari\n"
            . "- Mood positif: {$moodPositivePct}%\n"
            . "- Mood datang: {$stats['mood_arrival_avg']}/5 (dominan: {$dominantMoodArr})\n"
            . "- Mood pulang: {$stats['mood_end_avg']}/5 (dominan: {$dominantMoodEnd})\n"
            . "- Tren mood: {$dominantMoodTrend}\n"
            . "- Energi datang: {$dominantEnergyArr}\n"
            . "- Energi pulang: {$dominantEnergyEnd}\n"
            . "- Kemandirian: {$dominantIndependence}\n"
            . "- Perilaku: {$dominantBehavior}\n"
            . "- Kendala: {$dominantChallenge}\n"
            . "- Komunikasi: {$dominantCommMode}, inisiatif {$dominantCommInitiative}\n"
            . "- Interaksi guru: {$dominantSocialTeacher}\n"
            . "- Interaksi teman: {$dominantSocialPeers}\n"
            . "- Pencapaian: {$dominantAchievement}\n"
            . "- Kegiatan: " . ($topActivities ?: 'tidak ada data') . "\n"
            . "- Solusi: " . ($topSolutions ?: 'tidak ada data') . "\n"
            . "- Skor umum: {$dominantOverall}\n"
            . "- Catatan guru: " . ($otherNotes ?: 'tidak ada catatan tambahan') . "\n\n"

            . "FORMAT OUTPUT (ikuti persis, tidak ada teks lain):\n\n"
            . "HEADLINE:\n"
            . "[Kalimat ringkas max 40 karakter, personal, contoh: '{$firstName} bulan ini makin mandiri']\n\n"
            . "HEADLINE_EMOJI:\n"
            . "[Satu emoji yang merepresentasikan bulan ini, contoh: 🌱]\n\n"
            . "ATTENTION_TREND:\n"
            . "[improving ATAU stable ATAU worsening — pilih satu]\n\n"
            . "ATTENTION_NOTE:\n"
            . "[Satu kalimat konteks tren max 80 karakter]\n\n"
            . "RINGKASAN:\n"
            . "[3 kalimat tentang perkembangan anak bulan ini]\n\n"
            . "PERHATIAN:\n"
            . "[2 kalimat hal yang perlu diperhatikan orang tua]\n\n"
            . "REKOMENDASI:\n"
            . "[2 kalimat saran untuk bulan depan]";

        $requestBody = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 3000],
        ];

        try {
            Log::info("Generate AI report untuk {$student->name}");

            $response = $this->callGemini($requestBody);

            if ($response->status() === 429) {
                $retryAfter = $response->json('error.details.2.retryDelay', '60s');
                $seconds    = (int) filter_var($retryAfter, FILTER_SANITIZE_NUMBER_INT);
                $waitTime   = max($seconds + 5, 60);
                Log::warning("Rate limit Gemini. Retry {$waitTime} detik...");
                sleep($waitTime);
                $response = $this->callGemini($requestBody);
            }

            if ($response->failed()) {
                throw new \Exception("Gemini gagal. Status: {$response->status()} Body: {$response->body()}");
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');

            if (empty(trim($text))) {
                throw new \Exception("Teks kosong dari Gemini.");
            }

            return $this->parseAiOutput($text);

        } catch (\Exception $e) {
            Log::error("AI annual error untuk {$student->name}: " . $e->getMessage());
            return [
                'summary' => null, 'attention' => null, 'recommendation' => null,
                'headline' => null, 'headline_emoji' => null,
                'attention_trend' => null, 'attention_note' => null,
            ];
        }
    }

    // ─── Call Gemini ──────────────────────────────────────────────────────────

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
        $fields = [
            'headline'        => '/HEADLINE:\s*(.*?)(?=HEADLINE_EMOJI:|$)/si',
            'headline_emoji'  => '/HEADLINE_EMOJI:\s*(.*?)(?=ATTENTION_TREND:|$)/si',
            'attention_trend' => '/ATTENTION_TREND:\s*(.*?)(?=ATTENTION_NOTE:|$)/si',
            'attention_note'  => '/ATTENTION_NOTE:\s*(.*?)(?=RINGKASAN:|$)/si',
            'summary'         => '/RINGKASAN:\s*(.*?)(?=PERHATIAN:|$)/si',
            'attention'       => '/PERHATIAN:\s*(.*?)(?=REKOMENDASI:|$)/si',
            'recommendation'  => '/REKOMENDASI:\s*(.*?)$/si',
        ];

        $result = [];
        foreach ($fields as $key => $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $result[$key] = trim($m[1]) ?: null;
            } else {
                $result[$key] = null;
            }
        }

        // Validasi attention_trend — harus salah satu dari 3 nilai
        if (!in_array($result['attention_trend'], ['improving', 'stable', 'worsening'])) {
            $result['attention_trend'] = 'stable';
        }

        // Fallback kalau semua null
        if (empty(array_filter($result))) {
            Log::warning("Parse gagal, simpan raw text.");
            return [
                'summary' => $text, 'attention' => null, 'recommendation' => null,
                'headline' => null, 'headline_emoji' => null,
                'attention_trend' => 'stable', 'attention_note' => null,
            ];
        }

        return $result;
    }

    // ─── Helper dominant ─────────────────────────────────────────────────────

    private function getDominant(array $stats): string
    {
        if (empty($stats)) return '-';
        $key = array_key_first($stats);
        $pct = is_array($stats[$key]) ? ($stats[$key]['percent'] ?? 0) : $stats[$key];
        return "{$key} ({$pct}%)";
    }

    // ─── Generate semua murid ────────────────────────────────────────────────

    public function generateForAllStudents(int $month, int $year): array
    {
        $results    = [];
        $studentIds = DailyReport::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->distinct()
            ->pluck('student_id');

        Log::info("Generate monthly report {$month}/{$year}");

        foreach ($studentIds as $index => $studentId) {
            if ($index > 0) {
                Log::info("Delay 15 detik sebelum murid ke-" . ($index + 1));
                sleep(15);
            }

            try {
                $report    = $this->generate($studentId, $month, $year);
                $results[] = ['student_id' => $studentId, 'status' => 'success', 'report_id' => $report->id];
                Log::info("Sukses generate student_id {$studentId}");
            } catch (\Exception $e) {
                $results[] = ['student_id' => $studentId, 'status' => 'failed', 'error' => $e->getMessage()];
                Log::error("Gagal student_id {$studentId}: " . $e->getMessage());
            }
        }

        return $results;
    }
}
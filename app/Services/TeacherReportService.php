<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\SchoolHoliday;
use App\Models\TeacherMonthlyReport;
use App\Models\TeacherAnnualReport;
use App\Models\TeacherStudentPeriod;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeacherReportService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model  = config('services.gemini.model', 'gemini-2.0-flash');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    public function getAcademicYear(int $month, int $year): string
    {
        if ($month >= 7) {
            return $year . '/' . ($year + 1);
        }
        return ($year - 1) . '/' . $year;
    }

    public function getEffectiveWorkingDays(int $month, int $year): int
    {
        $start    = Carbon::create($year, $month, 1)->startOfMonth();
        $end      = Carbon::create($year, $month, 1)->endOfMonth();

        $holidays = SchoolHoliday::whereBetween('date', [$start, $end])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $workingDays = 0;
        $current     = $start->copy();

        while ($current->lte($end)) {
            if ($current->dayOfWeek !== 0 && !in_array($current->toDateString(), $holidays)) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    public function syncPeriods(int $month, int $year): void
    {
        $academicYear = $this->getAcademicYear($month, $year);
        $startedAt    = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();

        $classes = \App\Models\ClassRoom::with('students:id')->get();
        foreach ($classes as $class) {
            if (!$class->homeroom_teacher_id) continue;
            foreach ($class->students as $student) {
                TeacherStudentPeriod::updateOrCreate(
                    [
                        'teacher_id'    => $class->homeroom_teacher_id,
                        'student_id'    => $student->id,
                        'academic_year' => $academicYear,
                        'role_type'     => 'homeroom',
                    ],
                    ['is_active' => true, 'started_at' => $startedAt]
                );
            }
        }

        $shadowGroups = \App\Models\ShadowGroup::all();
        foreach ($shadowGroups as $group) {
            if ($group->pic_id) {
                TeacherStudentPeriod::updateOrCreate(
                    [
                        'teacher_id'    => $group->pic_id,
                        'student_id'    => $group->student_id,
                        'academic_year' => $academicYear,
                        'role_type'     => 'shadow_pj',
                    ],
                    ['is_active' => true, 'started_at' => $startedAt]
                );
            }
            if ($group->partner_id) {
                TeacherStudentPeriod::updateOrCreate(
                    [
                        'teacher_id'    => $group->partner_id,
                        'student_id'    => $group->student_id,
                        'academic_year' => $academicYear,
                        'role_type'     => 'shadow_teacher',
                    ],
                    ['is_active' => true, 'started_at' => $startedAt]
                );
            }
        }

        $oneOnOneGroups = \App\Models\OneOnOneGroup::all();
        foreach ($oneOnOneGroups as $group) {
            if ($group->teacher_id) {
                TeacherStudentPeriod::updateOrCreate(
                    [
                        'teacher_id'    => $group->teacher_id,
                        'student_id'    => $group->student_id,
                        'academic_year' => $academicYear,
                        'role_type'     => 'therapist',
                    ],
                    ['is_active' => true, 'started_at' => $startedAt]
                );
            }
        }

        Log::info("Sync periods selesai untuk {$month}/{$year} - {$academicYear}");
    }

    public function generateMonthly(int $teacherId, int $month, int $year): TeacherMonthlyReport
    {
        $teacher      = User::findOrFail($teacherId);
        $academicYear = $this->getAcademicYear($month, $year);
        $startDate    = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate      = Carbon::create($year, $month, 1)->endOfMonth();

        $reports = DailyReport::with('detail')
            ->where(function ($q) use ($teacherId) {
                $q->where('shadow_teacher_id', $teacherId)
                  ->orWhere('therapist_id', $teacherId);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalTeachingDays  = $this->getEffectiveWorkingDays($month, $year);
        $totalReports       = $reports->count();
        $totalMissingDays   = max(0, $totalTeachingDays - $totalReports);
        $avgReportLength    = $reports->avg(fn($r) => $r->detail?->text_length ?? 0);
        $completenessScore  = $totalTeachingDays > 0
            ? round(($totalReports / $totalTeachingDays) * 100, 2)
            : 0;

        $aiOutput = $this->generateAiScoring($teacher, $reports, $month, $year, [
            'total_teaching_days'  => $totalTeachingDays,
            'total_reports'        => $totalReports,
            'total_missing_days'   => $totalMissingDays,
            'avg_report_length'    => round($avgReportLength, 2),
            'completeness_score'   => $completenessScore,
        ]);

        $report = TeacherMonthlyReport::updateOrCreate(
            ['teacher_id' => $teacherId, 'month' => $month, 'year' => $year],
            [
                'academic_year'          => $academicYear,
                'total_teaching_days'    => $totalTeachingDays,
                'total_reports_created'  => $totalReports,
                'total_missing_days'     => $totalMissingDays,
                'avg_report_length'      => round($avgReportLength, 2),
                'observation_score'      => $aiOutput['observation_score'],
                'analysis_score'         => $aiOutput['analysis_score'],
                'solution_score'         => $aiOutput['solution_score'],
                'completeness_score'     => $completenessScore,
                'ai_improvement_areas'   => $aiOutput['improvement_areas'],
                'ai_performance_summary' => $aiOutput['summary'],
                'performance_indicator'  => $this->calcPerformanceIndicator($aiOutput, $completenessScore),
                'status'                 => 'generated',
                'generated_at'           => now(),
            ]
        );

        return $report;
    }

    public function generateAnnual(int $teacherId, string $academicYear): TeacherAnnualReport
    {
        $teacher = User::findOrFail($teacherId);

        $monthlyReports = TeacherMonthlyReport::where('teacher_id', $teacherId)
            ->where('academic_year', $academicYear)
            ->where('status', 'generated')
            ->get();

        if ($monthlyReports->isEmpty()) {
            throw new \Exception("Tidak ada monthly report untuk {$teacher->name} tahun ajaran {$academicYear}.");
        }

        $totalTeachingDays   = $monthlyReports->sum('total_teaching_days');
        $totalReports        = $monthlyReports->sum('total_reports_created');
        $totalMissingDays    = $monthlyReports->sum('total_missing_days');
        $avgReportLength     = $monthlyReports->avg('avg_report_length');
        $avgObservation      = $monthlyReports->avg('observation_score');
        $avgAnalysis         = $monthlyReports->avg('analysis_score');
        $avgSolution         = $monthlyReports->avg('solution_score');
        $avgCompleteness     = $monthlyReports->avg('completeness_score');

        $aiOutput = $this->generateAiAnnualSummary($teacher, $monthlyReports, $academicYear);

        $report = TeacherAnnualReport::updateOrCreate(
            ['teacher_id' => $teacherId, 'academic_year' => $academicYear],
            [
                'total_teaching_days_year'    => $totalTeachingDays,
                'total_reports_created_year'  => $totalReports,
                'total_missing_days_year'     => $totalMissingDays,
                'avg_report_length_year'      => round($avgReportLength, 2),
                'avg_observation_score'       => round($avgObservation, 2),
                'avg_analysis_score'          => round($avgAnalysis, 2),
                'avg_solution_score'          => round($avgSolution, 2),
                'avg_completeness_score'      => round($avgCompleteness, 2),
                'ai_annual_summary'           => $aiOutput['summary'],
                'ai_annual_improvement_areas' => $aiOutput['improvement_areas'],
                'annual_performance_indicator'=> $this->calcAnnualIndicator($avgObservation, $avgAnalysis, $avgSolution, $avgCompleteness),
                'status'                      => 'generated',
                'generated_at'                => now(),
            ]
        );

        return $report;
    }

    private function generateAiScoring($teacher, $reports, int $month, int $year, array $stats): array
    {
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',    4 => 'April',
            5 => 'Mei',     6 => 'Juni',     7 => 'Juli',      8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $sampleNotes = $reports->take(5)->map(function ($r) {
            $d = $r->detail;
            if (!$d) return null;
            return implode(' | ', array_filter([
                $d->activity_notes  ?? null,
                $d->solution_notes  ?? null,
                $d->challenge_other ?? null,
            ]));
        })->filter()->implode("\n");

        $prompt = "Kamu adalah evaluator kinerja guru di Sekolah Berkebutuhan Khusus Lentera Fajar.\n"
            . "Evaluasi kinerja guru berdasarkan data laporan harian bulan ini.\n"
            . "WAJIB kembalikan HANYA JSON valid, tanpa teks lain.\n\n"
            . "Data {$teacher->name}, {$bulanIndo[$month]} {$year}:\n"
            . "- Hari kerja efektif: {$stats['total_teaching_days']} hari\n"
            . "- Laporan dibuat: {$stats['total_reports']} laporan\n"
            . "- Hari tidak laporan: {$stats['total_missing_days']} hari\n"
            . "- Rata-rata panjang laporan: {$stats['avg_report_length']} kata\n"
            . "- Kelengkapan laporan: {$stats['completeness_score']}%\n"
            . "- Sampel isi laporan:\n{$sampleNotes}\n\n"
            . "Berikan skor 1.00-5.00 untuk:\n"
            . "- observation_score: kejelasan observasi perilaku/kondisi murid\n"
            . "- analysis_score: kedalaman analisis perkembangan murid\n"
            . "- solution_score: kejelasan solusi/tindakan yang dilakukan\n\n"
            . "FORMAT JSON (kembalikan persis ini):\n"
            . "{\n"
            . "  \"observation_score\": 3.50,\n"
            . "  \"analysis_score\": 3.00,\n"
            . "  \"solution_score\": 3.50,\n"
            . "  \"summary\": \"2 kalimat ringkasan performa guru bulan ini\",\n"
            . "  \"improvement_areas\": [\"poin 1\", \"poin 2\", \"poin 3\"]\n"
            . "}";

        try {
            $response = $this->callGemini([
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 1000],
            ]);

            if ($response->failed()) {
                throw new \Exception("Gemini gagal. Status: " . $response->status() . " Body: " . $response->body());
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');
            $text = preg_replace('/```json|```/', '', $text);

            // Ambil hanya bagian JSON yang valid
            preg_match('/\{.*\}/s', $text, $jsonMatch);
            $text = $jsonMatch[0] ?? $text;
            $data = json_decode(trim($text), true);

            if (!$data) throw new \Exception("Parse JSON gagal: " . $text);

            return [
                'observation_score'  => $data['observation_score'] ?? 3.0,
                'analysis_score'     => $data['analysis_score'] ?? 3.0,
                'solution_score'     => $data['solution_score'] ?? 3.0,
                'summary'            => $data['summary'] ?? null,
                'improvement_areas'  => $data['improvement_areas'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error("AI scoring error untuk {$teacher->name}: " . $e->getMessage());
            return [
                'observation_score' => null,
                'analysis_score'    => null,
                'solution_score'    => null,
                'summary'           => null,
                'improvement_areas' => [],
            ];
        }
    }

    private function generateAiAnnualSummary($teacher, $monthlyReports, string $academicYear): array
    {
        $avgObs  = round($monthlyReports->avg('observation_score'), 2);
        $avgAna  = round($monthlyReports->avg('analysis_score'), 2);
        $avgSol  = round($monthlyReports->avg('solution_score'), 2);
        $avgComp = round($monthlyReports->avg('completeness_score'), 2);
        $total   = $monthlyReports->sum('total_reports_created');
        $missing = $monthlyReports->sum('total_missing_days');

        $prompt = "Kamu adalah evaluator tahunan Sekolah Berkebutuhan Khusus Lentera Fajar.\n"
            . "WAJIB kembalikan HANYA JSON valid.\n\n"
            . "Ringkasan kinerja {$teacher->name} tahun ajaran {$academicYear}:\n"
            . "- Total laporan dibuat: {$total}\n"
            . "- Total hari tidak laporan: {$missing}\n"
            . "- Rata-rata skor observasi: {$avgObs}/5\n"
            . "- Rata-rata skor analisis: {$avgAna}/5\n"
            . "- Rata-rata skor solusi: {$avgSol}/5\n"
            . "- Rata-rata kelengkapan: {$avgComp}%\n\n"
            . "FORMAT JSON:\n"
            . "{\n"
            . "  \"summary\": \"3 kalimat ringkasan performa tahunan\",\n"
            . "  \"improvement_areas\": [\"poin 1\", \"poin 2\", \"poin 3\"]\n"
            . "}";

        try {
            $response = $this->callGemini([
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 1000],
            ]);

            if ($response->failed()) {
                throw new \Exception("Gemini gagal. Status: " . $response->status() . " Body: " . $response->body());
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');
            $text = preg_replace('/```json|```/', '', $text);

            // Ambil hanya bagian JSON yang valid
            preg_match('/\{.*\}/s', $text, $jsonMatch);
            $text = $jsonMatch[0] ?? $text;
            $data = json_decode(trim($text), true);

            if (!$data) throw new \Exception("Parse JSON gagal: " . $text);

            return [
                'summary'           => $data['summary'] ?? null,
                'improvement_areas' => $data['improvement_areas'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error("AI annual error untuk {$teacher->name}: " . $e->getMessage());
            return ['summary' => null, 'improvement_areas' => []];
        }
    }

    private function calcPerformanceIndicator(array $aiOutput, float $completeness): string
    {
        $scores = array_filter([
            $aiOutput['observation_score'] ?? null,
            $aiOutput['analysis_score']    ?? null,
            $aiOutput['solution_score']    ?? null,
        ]);

        $avgScore = !empty($scores) ? array_sum($scores) / count($scores) : 3.0;
        $combined = ($avgScore / 5 * 70) + ($completeness / 100 * 30);

        return match(true) {
            $combined >= 85 => 'sangat_baik',
            $combined >= 70 => 'baik',
            $combined >= 55 => 'cukup',
            $combined >= 40 => 'kurang',
            default         => 'sangat_kurang',
        };
    }

    private function calcAnnualIndicator(
        ?float $obs, ?float $ana, ?float $sol, ?float $comp
    ): string {
        $scores = array_filter([$obs, $ana, $sol]);
        $avg    = !empty($scores) ? array_sum($scores) / count($scores) : 3.0;
        $combined = ($avg / 5 * 70) + (($comp ?? 0) / 100 * 30);

        return match(true) {
            $combined >= 85 => 'sangat_baik',
            $combined >= 70 => 'baik',
            $combined >= 55 => 'cukup',
            $combined >= 40 => 'kurang',
            default         => 'sangat_kurang',
        };
    }

    private function callGemini(array $body)
    {
        return Http::withoutVerifying()
            ->timeout(90)
            ->connectTimeout(30)
            ->post("{$this->apiUrl}?key={$this->apiKey}", $body);
    }

    public function generateMonthlyForAll(int $month, int $year): array
    {
        $this->syncPeriods($month, $year);

        $results    = [];
        $teacherIds = DailyReport::whereBetween('date', [
                Carbon::create($year, $month, 1)->startOfMonth(),
                Carbon::create($year, $month, 1)->endOfMonth(),
            ])
            ->selectRaw('COALESCE(shadow_teacher_id, therapist_id) as teacher_id')
            ->whereNotNull('shadow_teacher_id')
            ->orWhereNotNull('therapist_id')
            ->distinct()
            ->pluck('teacher_id')
            ->filter()
            ->unique();

        foreach ($teacherIds as $index => $teacherId) {
            if ($index > 0) sleep(15);

            try {
                $report    = $this->generateMonthly($teacherId, $month, $year);
                $results[] = ['teacher_id' => $teacherId, 'status' => 'success', 'report_id' => $report->id];
            } catch (\Exception $e) {
                $results[] = ['teacher_id' => $teacherId, 'status' => 'failed', 'error' => $e->getMessage()];
                Log::error("Gagal generate teacher monthly report teacher_id {$teacherId}: " . $e->getMessage());
            }
        }

        return $results;
    }

    public function generateAnnualForAll(string $academicYear): array
    {
        $results    = [];
        $teacherIds = TeacherMonthlyReport::where('academic_year', $academicYear)
            ->where('status', 'generated')
            ->distinct()
            ->pluck('teacher_id');

        foreach ($teacherIds as $index => $teacherId) {
            if ($index > 0) sleep(15);

            try {
                $report    = $this->generateAnnual($teacherId, $academicYear);
                $results[] = ['teacher_id' => $teacherId, 'status' => 'success', 'report_id' => $report->id];
            } catch (\Exception $e) {
                $results[] = ['teacher_id' => $teacherId, 'status' => 'failed', 'error' => $e->getMessage()];
                Log::error("Gagal generate teacher annual report teacher_id {$teacherId}: " . $e->getMessage());
            }
        }

        return $results;
    }
}
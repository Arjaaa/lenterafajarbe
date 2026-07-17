<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\SchoolHoliday;
use App\Models\StudentDocumentation;
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

    // ✅ FIX: konstanta timezone supaya konsisten di seluruh service
    private const TZ = 'Asia/Jakarta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model  = config('services.gemini.model', 'gemini-2.5-flash');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1/models/{$this->model}:generateContent";
    }

    // ─── Helper: hitung tahun ajaran ─────────────────────────────────────────

    public function getAcademicYear(int $month, int $year): string
    {
        return $month >= 7
            ? $year . '/' . ($year + 1)
            : ($year - 1) . '/' . $year;
    }

    // ─── Helper: buat Carbon dengan timezone WIB ──────────────────────────────
    // ✅ FIX: semua pembuatan Carbon untuk rentang tanggal wajib lewat helper ini
    // supaya tidak ada yang lupa timezone dan geser 7 jam ke UTC.

    private function makeDate(int $year, int $month, int $day = 1): Carbon
    {
        return Carbon::create($year, $month, $day, 0, 0, 0, self::TZ);
    }

    private function parseDate(string $date): Carbon
    {
        return Carbon::parse($date, self::TZ)->startOfDay();
    }

    // ─── Helper: hitung hari kerja efektif dalam rentang tanggal ─────────────

    public function getEffectiveWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $holidays = SchoolHoliday::whereBetween('date', [
            $startDate->toDateString(),
            $endDate->toDateString(),
        ])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $workingDays = 0;
        $current     = $startDate->copy()->startOfDay();

        while ($current->lte($endDate)) {
            if ($current->dayOfWeek !== 0 && !in_array($current->toDateString(), $holidays)) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    // ─── Deteksi otomatis rentang aktif seorang guru dalam bulan tertentu ────

    private function resolvePeriodRange(int $teacherId, int $month, int $year): array
    {
        // ✅ FIX: pakai makeDate() supaya timezone WIB
        $monthStart   = $this->makeDate($year, $month, 1)->startOfMonth();
        $monthEnd     = $this->makeDate($year, $month, 1)->endOfMonth();
        $academicYear = $this->getAcademicYear($month, $year);

        $periods = TeacherStudentPeriod::where('teacher_id', $teacherId)
            ->where('academic_year', $academicYear)
            ->where('started_at', '<=', $monthEnd->toDateString())
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', $monthStart->toDateString());
            })
            ->get();

        if ($periods->isEmpty()) {
            return [$monthStart, $monthEnd, false];
        }

        $earliestStart = $this->parseDate($periods->min('started_at'));
        $stillActive   = $periods->contains(fn($p) => is_null($p->ended_at));
        $latestEnd     = $periods->whereNotNull('ended_at')->max('ended_at');

        $effectiveStart = $earliestStart->max($monthStart);
        $effectiveEnd   = ($stillActive || !$latestEnd)
            ? $monthEnd
            : $this->parseDate($latestEnd)->min($monthEnd);

        $isPartial = !$effectiveStart->toDateString() === $monthStart->toDateString()
            || !$effectiveEnd->toDateString() === $monthEnd->toDateString();

        // ✅ cara aman bandingkan tanggal (bukan objek Carbon langsung)
        $isPartial = $effectiveStart->toDateString() !== $monthStart->toDateString()
            || $effectiveEnd->toDateString() !== $monthEnd->toDateString();

        return [$effectiveStart, $effectiveEnd, $isPartial];
    }

    // ─── Mulai periode guru ───────────────────────────────────────────────────

    public function startPeriod(
        int $teacherId,
        int $studentId,
        string $roleType,
        string $startedAt,
        string $academicYear
    ): TeacherStudentPeriod {
        $period = TeacherStudentPeriod::updateOrCreate(
            [
                'teacher_id'    => $teacherId,
                'student_id'    => $studentId,
                'academic_year' => $academicYear,
                'role_type'     => $roleType,
            ],
            [
                'is_active'  => true,
                'started_at' => $startedAt,
                'ended_at'   => null,
            ]
        );

        Log::info("Mulai periode guru {$teacherId} untuk student {$studentId} ({$roleType}) sejak {$startedAt}");

        return $period;
    }

    // ─── Stop guru — deactivate period + generate partial report ─────────────

    public function stopTeacher(TeacherStudentPeriod $period, string $endedAt): TeacherMonthlyReport
    {
        $period->update([
            'is_active' => false,
            'ended_at'  => $endedAt,
        ]);

        // ✅ FIX: parse endedAt dengan timezone WIB
        $endDate    = $this->parseDate($endedAt);
        $month      = $endDate->month;
        $year       = $endDate->year;
        $monthStart = $this->makeDate($year, $month, 1)->startOfMonth();

        $start = $this->parseDate($period->started_at)->max($monthStart);

        Log::info("Stop guru {$period->teacher_id}, generate partial report {$start->toDateString()} - {$endedAt}");

        return $this->generateMonthly(
            teacherId: $period->teacher_id,
            month:     $month,
            year:      $year,
            startDate: $start->toDateString(),
            endDate:   $endedAt,
            isPartial: true,
        );
    }

    // ─── Sync teacher_student_periods dari relasi yang ada ────────────────────

    public function syncPeriods(int $month, int $year): void
    {
        $academicYear = $this->getAcademicYear($month, $year);
        // ✅ FIX: pakai makeDate() supaya timezone WIB
        $startedAt    = $this->makeDate($year, $month, 1)->startOfMonth()->toDateString();

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

    // ─── Generate monthly report untuk 1 guru ────────────────────────────────

    public function generateMonthly(
        int $teacherId,
        int $month,
        int $year,
        ?string $startDate = null,
        ?string $endDate   = null,
        ?bool $isPartial   = null
    ): TeacherMonthlyReport {

        $teacher      = User::findOrFail($teacherId);
        $academicYear = $this->getAcademicYear($month, $year);

        // ✅ FIX: makeDate() supaya bulan penuh dihitung dalam WIB, bukan UTC
        $monthStart = $this->makeDate($year, $month, 1)->startOfMonth();
        $monthEnd   = $this->makeDate($year, $month, 1)->endOfMonth();

        if ($startDate && $endDate) {
            // ✅ FIX: parseDate() supaya start/end dari parameter juga dalam WIB
            $start     = $this->parseDate($startDate);
            $end       = $this->parseDate($endDate)->endOfDay();
            $isPartial = $isPartial ?? (
                $start->toDateString() !== $monthStart->toDateString() ||
                $end->toDateString()   !== $monthEnd->toDateString()
            );
        } else {
            [$start, $end, $autoPartial] = $this->resolvePeriodRange($teacherId, $month, $year);
            $isPartial = $isPartial ?? $autoPartial;
        }

        // ✅ FIX: whereBetween pakai toDateString() supaya query DB berdasarkan
        // tanggal (DATE), bukan datetime UTC — menghindari geser 7 jam
        $allReports = DailyReport::with(['detail', 'classification'])
            ->where(function ($q) use ($teacherId) {
                $q->where('shadow_teacher_id', $teacherId)
                  ->orWhere('therapist_id', $teacherId);
            })
            ->whereRaw("DATE(date) BETWEEN ? AND ?", [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->orderBy('date')
            ->get();

        $absentReports  = $allReports->where('attendance_status', '!=', 'hadir');
        $presentReports = $allReports->where('attendance_status', 'hadir');

        $totalTeachingDays = $this->getEffectiveWorkingDays($start, $end);
        $totalAbsentDays   = $absentReports->count();
        $totalReports      = $presentReports->count();
        $totalMissingDays  = max(0, $totalTeachingDays - $allReports->count());

        $avgReportLength   = (float) ($presentReports->avg(fn($r) => $r->detail?->text_length ?? 0) ?? 0);
        $completenessScore = $totalTeachingDays > 0
            ? round(($allReports->count() / $totalTeachingDays) * 100, 2)
            : 0;

        $details         = $presentReports->pluck('detail')->filter();
        $classifications = $presentReports->pluck('classification')->filter();

        // Laporan Harian
        $reportCompletenessPct = $this->calcReportCompleteness($details, $totalReports);
        $timeliness            = $this->calcTimeliness($presentReports);
        $weeklyConsistency     = $this->calcWeeklyConsistency($allReports, $start, $end);
        $longestStreak         = $this->calcLongestStreak($allReports, $start, $end);
        $avgFillTime           = $this->calcAvgFillTime($presentReports);

        // Kondisi & Mood
        $physicalHealthPct  = $details->isNotEmpty()
            ? round($details->where('physical_condition_arrival', 'sehat')->count() / $details->count() * 100, 2)
            : 0;
        $moodPositivePct    = $details->isNotEmpty()
            ? round($details->filter(fn($d) => ($d->mood_arrival ?? 0) >= 4)->count() / $details->count() * 100, 2)
            : 0;
        $moodConsistencyPct = $details->isNotEmpty()
            ? round($details->filter(fn($d) => $d->mood_arrival && $d->mood_end)->count() / $details->count() * 100, 2)
            : 0;
        $totalChallenges    = $details->filter(fn($d) => !empty($d->challenge))->count();
        $totalSolutions     = $details->filter(fn($d) => !empty($d->solution_notes))->count();

        // Worksheet
        $totalWorksheets        = $details->where('has_homework', true)->count();
        $worksheetSubmissionPct = $totalReports > 0 ? round($totalWorksheets / $totalReports * 100, 2) : 0;
        $worksheetStudentCount  = $presentReports->filter(fn($r) => $r->detail?->has_homework)->pluck('student_id')->unique()->count();
        $worksheetPerStudentAvg = $worksheetStudentCount > 0 ? round($totalWorksheets / $worksheetStudentCount, 2) : 0;
        $worksheetReports       = $presentReports->filter(fn($r) => $r->detail?->has_homework);
        $worksheetTimelinessPct = $worksheetReports->isNotEmpty()
            ? round(
                $worksheetReports->filter(function ($r) {
                    return Carbon::parse($r->created_at)->lte(Carbon::parse($r->date)->addDay());
                })->count() / $worksheetReports->count() * 100, 2
            ) : 0;

        // Dokumentasi
        $studentIds = $allReports->pluck('student_id')->filter()->unique()->toArray();
        $docStats   = $this->calcDocumentationStats($teacherId, $studentIds, $start, $end, $totalReports);

        // Siswa
        $activeStudentCount         = $allReports->pluck('student_id')->filter()->unique()->count();
        $studentsNoReportThisWeek   = $this->calcStudentsNoReportThisWeek($allReports);
        $studentPositiveProgressPct = $this->calcStudentPositiveProgress($classifications, $activeStudentCount);
        $reportsPerStudentAvg       = $activeStudentCount > 0 ? round($totalReports / $activeStudentCount, 2) : 0;

        // AI Scoring
        $aiOutput = $this->generateAiScoring($teacher, $presentReports, $month, $year, [
            'total_teaching_days' => $totalTeachingDays,
            'total_reports'       => $totalReports,
            'total_absent_days'   => $totalAbsentDays,
            'total_missing_days'  => $totalMissingDays,
            'avg_report_length'   => round($avgReportLength, 2),
            'completeness_score'  => $completenessScore,
            'period_start'        => $start->toDateString(),
            'period_end'          => $end->toDateString(),
            'is_partial'          => $isPartial,
        ]);

        $payload = [
            'academic_year'                  => $academicYear,
            'period_start'                   => $start->toDateString(),
            'period_end'                     => $end->toDateString(),
            'is_partial'                     => $isPartial,
            'total_teaching_days'            => $totalTeachingDays,
            'total_reports_created'          => $totalReports,
            'total_absent_days'              => $totalAbsentDays,
            'total_missing_days'             => $totalMissingDays,
            'avg_report_length'              => round($avgReportLength, 2),
            'observation_score'              => $aiOutput['observation_score'],
            'analysis_score'                 => $aiOutput['analysis_score'],
            'solution_score'                 => $aiOutput['solution_score'],
            'completeness_score'             => $completenessScore,
            'report_completeness_pct'        => $reportCompletenessPct,
            'timeliness_score'               => $timeliness,
            'weekly_consistency'             => $weeklyConsistency,
            'longest_streak'                 => $longestStreak,
            'avg_fill_time_minutes'          => $avgFillTime,
            'physical_health_pct'            => $physicalHealthPct,
            'mood_positive_pct'              => $moodPositivePct,
            'mood_consistency_pct'           => $moodConsistencyPct,
            'total_challenges_recorded'      => $totalChallenges,
            'total_solutions_recorded'       => $totalSolutions,
            'worksheet_submission_pct'       => $worksheetSubmissionPct,
            'worksheet_timeliness_pct'       => $worksheetTimelinessPct,
            'total_worksheets'               => $totalWorksheets,
            'worksheet_student_count'        => $worksheetStudentCount,
            'worksheet_per_student_avg'      => $worksheetPerStudentAvg,
            'documentation_pct'              => $docStats['documentation_pct'],
            'docs_per_report_avg'            => $docStats['docs_per_report_avg'],
            'documented_weeks'               => $docStats['documented_weeks'],
            'active_student_count'           => $activeStudentCount,
            'students_no_report_this_week'   => $studentsNoReportThisWeek,
            'student_positive_progress_pct'  => $studentPositiveProgressPct,
            'reports_per_student_avg'        => $reportsPerStudentAvg,
            'ai_improvement_areas'           => $aiOutput['improvement_areas'],
            'ai_performance_summary'         => $aiOutput['summary'],
            'performance_indicator'          => $this->calcPerformanceIndicator($aiOutput, $completenessScore),
            'status'                         => 'generated',
            'generated_at'                   => now(),
        ];

        $key = $isPartial
            ? [
                'teacher_id'   => $teacherId,
                'month'        => $month,
                'year'         => $year,
                'period_start' => $start->toDateString(),
              ]
            : [
                'teacher_id' => $teacherId,
                'month'      => $month,
                'year'       => $year,
                'is_partial' => false,
              ];

        return TeacherMonthlyReport::updateOrCreate($key, $payload);
    }

    private function calcReportCompleteness($details, int $totalReports): float
    {
        if ($totalReports === 0) return 0;
        $completeCount = $details->filter(function ($d) {
            return $d->physical_condition_arrival && $d->mood_arrival && $d->mood_end && $d->behavior && $d->activity_notes;
        })->count();
        return round($completeCount / $totalReports * 100, 2);
    }

    private function calcTimeliness($reports): float
    {
        if ($reports->isEmpty()) return 0;
        $onTime = $reports->filter(function ($r) {
            return Carbon::parse($r->created_at)->lte(Carbon::parse($r->date)->endOfDay()->addHours(16));
        })->count();
        return round($onTime / $reports->count() * 100, 2);
    }

    private function calcWeeklyConsistency($reports, Carbon $start, Carbon $end): float
    {
        // ✅ FIX: parse date dari report juga pakai timezone WIB
        $reportDates     = $reports->pluck('date')->map(fn($d) => Carbon::parse($d, self::TZ)->startOfDay());
        $totalWeeks      = 0;
        $weeksWithReport = 0;
        $current         = $start->copy()->startOfWeek();

        while ($current->lte($end)) {
            $weekStart = $current->copy()->max($start);
            $weekEnd   = $current->copy()->endOfWeek()->min($end);
            $totalWeeks++;
            if ($reportDates->contains(fn($d) => $d->between($weekStart, $weekEnd))) {
                $weeksWithReport++;
            }
            $current->addWeek();
        }

        return $totalWeeks > 0 ? round($weeksWithReport / $totalWeeks * 100, 2) : 0;
    }

    private function calcLongestStreak($reports, Carbon $start, Carbon $end): int
    {
        $reportDates = $reports->pluck('date')
            ->map(fn($d) => Carbon::parse($d, self::TZ)->toDateString())
            ->unique()->sort()->values();

        if ($reportDates->isEmpty()) return 0;

        $holidays = SchoolHoliday::whereBetween('date', [
            $start->toDateString(),
            $end->toDateString(),
        ])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $longest = 0;
        $current = 0;
        $prev    = null;

        foreach ($reportDates as $dateStr) {
            $date = Carbon::parse($dateStr);
            if ($date->dayOfWeek === 0 || in_array($dateStr, $holidays)) continue;

            if ($prev === null) {
                $current = 1;
            } else {
                $current = Carbon::parse($prev)->diffInDays($date) === 1 ? $current + 1 : 1;
            }

            $longest = max($longest, $current);
            $prev    = $dateStr;
        }

        return $longest;
    }

    private function calcAvgFillTime($reports): ?float
    {
        $times = $reports->map(function ($r) {
            // ✅ FIX: startOfDay() pakai timezone WIB supaya "hari yang sama" dihitung benar
            $minutes = Carbon::parse($r->date, self::TZ)->startOfDay()
                ->diffInMinutes(Carbon::parse($r->created_at));
            return $minutes <= 1440 ? $minutes : null;
        })->filter();

        return $times->isNotEmpty() ? round($times->avg(), 2) : null;
    }

    private function calcDocumentationStats(int $teacherId, array $studentIds, Carbon $start, Carbon $end, int $totalReports): array
    {
        if (empty($studentIds)) {
            return ['documentation_pct' => 0, 'docs_per_report_avg' => 0, 'documented_weeks' => 0];
        }

        // ✅ FIX: pakai toDateString() supaya query DATE bukan datetime UTC
        $docs = StudentDocumentation::whereIn('student_id', $studentIds)
            ->where('uploaded_by', $teacherId)
            ->whereRaw("DATE(activity_date) BETWEEN ? AND ?", [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->get();

        $totalDocs        = $docs->count();
        $documentationPct = $totalReports > 0 ? round($totalDocs / $totalReports * 100, 2) : 0;
        $docsPerReport    = $totalReports > 0 ? round($totalDocs / $totalReports, 2) : 0;

        $docDates = $docs->pluck('activity_date')->map(fn($d) => Carbon::parse($d, self::TZ)->startOfDay());
        $docWeeks = 0;
        $current  = $start->copy()->startOfWeek();

        while ($current->lte($end)) {
            $weekStart = $current->copy()->max($start);
            $weekEnd   = $current->copy()->endOfWeek()->min($end);
            if ($docDates->contains(fn($d) => $d->between($weekStart, $weekEnd))) {
                $docWeeks++;
            }
            $current->addWeek();
        }

        return ['documentation_pct' => $documentationPct, 'docs_per_report_avg' => $docsPerReport, 'documented_weeks' => $docWeeks];
    }

    private function calcStudentsNoReportThisWeek($reports): int
    {
        // ✅ FIX: now() juga pakai timezone WIB
        $weekStart = Carbon::now(self::TZ)->startOfWeek();
        $weekEnd   = Carbon::now(self::TZ)->endOfWeek();

        $studentsWithReport = $reports->filter(
            fn($r) => Carbon::parse($r->date, self::TZ)->between($weekStart, $weekEnd)
        )->pluck('student_id')->unique();

        return $reports->pluck('student_id')->unique()->diff($studentsWithReport)->count();
    }

    private function calcStudentPositiveProgress($classifications, int $activeStudentCount): float
    {
        if ($activeStudentCount === 0 || $classifications->isEmpty()) return 0;
        $positiveCount = $classifications->filter(fn($c) => in_array($c->mood_trend ?? '', ['naik', 'stabil']))->count();
        return round($positiveCount / $classifications->count() * 100, 2);
    }

    // ─── Generate annual report untuk 1 guru ─────────────────────────────────

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

        $totalTeachingDays = $monthlyReports->sum('total_teaching_days');
        $totalReports      = $monthlyReports->sum('total_reports_created');
        $totalAbsentDays   = $monthlyReports->sum('total_absent_days');
        $totalMissingDays  = $monthlyReports->sum('total_missing_days');

        $avgReportLength = (float) ($monthlyReports->avg('avg_report_length') ?? 0);
        $avgObservation  = (float) ($monthlyReports->avg('observation_score') ?? 0);
        $avgAnalysis     = (float) ($monthlyReports->avg('analysis_score') ?? 0);
        $avgSolution     = (float) ($monthlyReports->avg('solution_score') ?? 0);
        $avgCompleteness = (float) ($monthlyReports->avg('completeness_score') ?? 0);

        $aiOutput = $this->generateAiAnnualSummary($teacher, $monthlyReports, $academicYear);

        $report = TeacherAnnualReport::updateOrCreate(
            ['teacher_id' => $teacherId, 'academic_year' => $academicYear],
            [
                'total_teaching_days_year'     => $totalTeachingDays,
                'total_reports_created_year'   => $totalReports,
                'total_absent_days_year'       => $totalAbsentDays,
                'total_missing_days_year'      => $totalMissingDays,
                'avg_report_length_year'       => round($avgReportLength, 2),
                'avg_observation_score'        => round($avgObservation, 2),
                'avg_analysis_score'           => round($avgAnalysis, 2),
                'avg_solution_score'           => round($avgSolution, 2),
                'avg_completeness_score'       => round($avgCompleteness, 2),
                'ai_annual_summary'            => $aiOutput['summary'],
                'ai_annual_improvement_areas'  => $aiOutput['improvement_areas'],
                'annual_performance_indicator' => $this->calcAnnualIndicator($avgObservation, $avgAnalysis, $avgSolution, $avgCompleteness),
                'status'                       => 'generated',
                'generated_at'                 => now(),
            ]
        );

        return $report;
    }

    private function parseGeminiJson(string $raw): ?array
    {
        $text = preg_replace('/```json|```/', '', $raw);
        preg_match('/\{.*\}/s', $text, $jsonMatch);
        $text = $jsonMatch[0] ?? $text;
        $text = preg_replace('/:\s*(\d+)\.\s*([,\}])/', ': $1.0$2', $text);
        $text = preg_replace('/,\s*\}/', '}', $text);
        $text = preg_replace('/,\s*\]/', ']', $text);
        $text = trim($text);

        $open  = substr_count($text, '{');
        $close = substr_count($text, '}');
        if ($open === 0 || $open !== $close) {
            Log::warning("parseGeminiJson: JSON terpotong. Raw: " . substr($raw, 0, 200));
            return null;
        }

        $data = json_decode($text, true);
        return $data ?: null;
    }

    private function generateAiScoring($teacher, $reports, int $month, int $year, array $stats): array
    {
        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $sampleNotes = $reports->take(5)->map(function ($r) {
            $d = $r->detail;
            if (!$d) return null;
            return implode(' | ', array_filter([$d->activity_notes ?? null, $d->solution_notes ?? null, $d->challenge_other ?? null]));
        })->filter()->implode("\n");

        $periodInfo = $stats['is_partial']
            ? "Periode: {$stats['period_start']} s/d {$stats['period_end']} (parsial — guru berhenti/mulai di tengah bulan, bukan full bulan)"
            : "Periode: full bulan {$bulanIndo[$month]} {$year}";

        $prompt = "Kamu adalah evaluator kinerja guru di Sekolah Berkebutuhan Khusus Lentera Fajar.\n"
            . "WAJIB kembalikan HANYA JSON valid, tanpa teks lain, tanpa markdown.\n\n"
            . "Data {$teacher->name}, {$bulanIndo[$month]} {$year}:\n"
            . "- {$periodInfo}\n"
            . "- Hari kerja efektif: {$stats['total_teaching_days']} hari\n"
            . "- Laporan dibuat: {$stats['total_reports']} laporan\n"
            . "- Hari murid tidak hadir: {$stats['total_absent_days']} hari\n"
            . "- Hari tidak laporan: {$stats['total_missing_days']} hari\n"
            . "- Rata-rata panjang laporan: {$stats['avg_report_length']} kata\n"
            . "- Kelengkapan laporan: {$stats['completeness_score']}%\n"
            . "- Sampel isi laporan:\n{$sampleNotes}\n\n"
            . "Berikan skor 1.00-5.00. Summary MAKSIMAL 50 karakter, improvement_areas MAKSIMAL 3 item singkat. Kembalikan JSON ini PERSIS:\n"
            . "{\"observation_score\":3.50,\"analysis_score\":3.00,\"solution_score\":3.50,\"summary\":\"Maks 50 karakter.\",\"improvement_areas\":[\"poin1\",\"poin2\",\"poin3\"]}";

        try {
            $response = $this->callGemini([
                'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 2048],
            ]);

            if ($response->failed()) {
                throw new \Exception("Gemini gagal. Status: " . $response->status() . " Body: " . $response->body());
            }

            $raw  = $response->json('candidates.0.content.parts.0.text', '');
            $data = $this->parseGeminiJson($raw);
            if (!$data) throw new \Exception("Parse JSON gagal: " . $raw);

            return [
                'observation_score' => $data['observation_score'] ?? null,
                'analysis_score'    => $data['analysis_score']    ?? null,
                'solution_score'    => $data['solution_score']    ?? null,
                'summary'           => $data['summary']           ?? null,
                'improvement_areas' => $data['improvement_areas'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error("AI scoring error untuk {$teacher->name}: " . $e->getMessage());
            return ['observation_score' => null, 'analysis_score' => null, 'solution_score' => null, 'summary' => null, 'improvement_areas' => []];
        }
    }

    private function generateAiAnnualSummary($teacher, $monthlyReports, string $academicYear): array
    {
        $avgObs  = round($monthlyReports->avg('observation_score') ?? 0, 2);
        $avgAna  = round($monthlyReports->avg('analysis_score') ?? 0, 2);
        $avgSol  = round($monthlyReports->avg('solution_score') ?? 0, 2);
        $avgComp = round($monthlyReports->avg('completeness_score') ?? 0, 2);
        $total   = $monthlyReports->sum('total_reports_created');
        $missing = $monthlyReports->sum('total_missing_days');
        $absent  = $monthlyReports->sum('total_absent_days');

        $prompt = "Kamu adalah evaluator tahunan Sekolah Berkebutuhan Khusus Lentera Fajar.\n"
            . "WAJIB kembalikan HANYA JSON valid, tanpa teks lain, tanpa markdown.\n\n"
            . "Kinerja {$teacher->name} tahun ajaran {$academicYear}:\n"
            . "- Total laporan: {$total}, Hari tidak laporan: {$missing}, Hari murid tidak hadir: {$absent}\n"
            . "- Skor observasi: {$avgObs}/5, analisis: {$avgAna}/5, solusi: {$avgSol}/5\n"
            . "- Kelengkapan: {$avgComp}%\n\n"
            . "Summary maksimal 80 karakter. Kembalikan JSON ini PERSIS:\n"
            . "{\"summary\":\"Ringkasan singkat.\",\"improvement_areas\":[\"poin1\",\"poin2\",\"poin3\"]}";

        try {
            $response = $this->callGemini([
                'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 2048],
            ]);

            if ($response->failed()) throw new \Exception("Gemini gagal. Status: " . $response->status());

            $raw  = $response->json('candidates.0.content.parts.0.text', '');
            $data = $this->parseGeminiJson($raw);
            if (!$data) throw new \Exception("Parse JSON gagal: " . $raw);

            return ['summary' => $data['summary'] ?? null, 'improvement_areas' => $data['improvement_areas'] ?? []];

        } catch (\Exception $e) {
            Log::error("AI annual error untuk {$teacher->name}: " . $e->getMessage());
            return ['summary' => null, 'improvement_areas' => []];
        }
    }

    private function calcPerformanceIndicator(array $aiOutput, float $completeness): string
    {
        $scores = array_filter([$aiOutput['observation_score'] ?? null, $aiOutput['analysis_score'] ?? null, $aiOutput['solution_score'] ?? null]);
        if (empty($scores)) return 'tidak_tersedia';

        $avgScore = array_sum($scores) / count($scores);
        $combined = ($avgScore / 5 * 70) + ($completeness / 100 * 30);

        return match(true) {
            $combined >= 85 => 'sangat_baik',
            $combined >= 70 => 'baik',
            $combined >= 55 => 'cukup',
            $combined >= 40 => 'kurang',
            default         => 'sangat_kurang',
        };
    }

    private function calcAnnualIndicator(?float $obs, ?float $ana, ?float $sol, ?float $comp): string
    {
        $scores = array_filter([$obs, $ana, $sol]);
        if (empty($scores)) return 'tidak_tersedia';

        $avg      = array_sum($scores) / count($scores);
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

    // ─── Generate untuk semua guru bulan ini ─────────────────────────────────

    public function generateMonthlyForAll(int $month, int $year): array
    {
        $this->syncPeriods($month, $year);

        $results      = [];
        // ✅ FIX: makeDate() supaya timezone WIB
        $monthStart   = $this->makeDate($year, $month, 1)->startOfMonth();
        $monthEnd     = $this->makeDate($year, $month, 1)->endOfMonth();
        $academicYear = $this->getAcademicYear($month, $year);

        $teacherIdsFromReports = DailyReport::whereRaw("DATE(date) BETWEEN ? AND ?", [
            $monthStart->toDateString(),
            $monthEnd->toDateString(),
        ])
            ->get(['shadow_teacher_id', 'therapist_id'])
            ->flatMap(fn($r) => array_filter([$r->shadow_teacher_id, $r->therapist_id]));

        $teacherIdsFromPeriods = TeacherStudentPeriod::where('academic_year', $academicYear)
            ->where('started_at', '<=', $monthEnd->toDateString())
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', $monthStart->toDateString());
            })
            ->pluck('teacher_id');

        $teacherIds = $teacherIdsFromReports->merge($teacherIdsFromPeriods)
            ->filter()
            ->unique()
            ->values();

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

    // ─── Generate annual untuk semua guru ────────────────────────────────────

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
                Log::error("Gagal generate teacher annual teacher_id {$teacherId}: " . $e->getMessage());
            }
        }

        return $results;
    }
}
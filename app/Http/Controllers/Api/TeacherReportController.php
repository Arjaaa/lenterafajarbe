<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeacherMonthlyReport;
use App\Models\TeacherAnnualReport;
use App\Models\TeacherStudentPeriod;
use App\Services\TeacherReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TeacherReportController extends Controller
{
    public function __construct(private TeacherReportService $service) {}

    private const PERFORMANCE_LABELS = [
        'sangat_baik'    => ['label' => 'Sangat Baik',   'color' => '#237804'],
        'baik'           => ['label' => 'Baik',          'color' => '#52C41A'],
        'cukup'          => ['label' => 'Cukup',         'color' => '#F5A623'],
        'kurang'         => ['label' => 'Kurang',        'color' => '#FF7A45'],
        'sangat_kurang'  => ['label' => 'Sangat Kurang', 'color' => '#FF4D4F'],
        'tidak_tersedia' => ['label' => 'Tidak Tersedia','color' => '#8C8C8C'],
    ];

    private const ROLE_LABELS = [
        'coordinator_main'      => 'Koordinator Utama',
        'coordinator_therapist' => 'Koordinator Terapis',
        'coordinator_shadow'    => 'Koordinator Shadow',
        'coordinator_wil'       => 'Koordinator Wilayah',
        'shadow_pj'             => 'Shadow PJ',
        'shadow_teacher'        => 'Guru Shadow',
        'therapist_homeroom'    => 'Guru Terapis',
        'therapist'             => 'Terapis',
    ];

    // ─── MONTHLY ──────────────────────────────────────────────────────────────

    // GET /api/teacher-reports/monthly
    public function monthlyIndex(Request $request)
    {
        $user = $request->user();

        $query = TeacherMonthlyReport::with('teacher:id,name,role')
            ->orderByDesc('year')->orderByDesc('month')->orderBy('period_start');

        if (!$user->isCoordinator()) {
            $visibleIds = $this->getVisibleTeacherIds($user);
            $query->whereIn('teacher_id', $visibleIds);
        }

        if ($request->has('month')) $query->where('month', $request->month);
        if ($request->has('year'))  $query->where('year', $request->year);

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    // GET /api/teacher-reports/monthly/{id}
    public function monthlyShow(Request $request, $id)
    {
        $user   = $request->user();
        $report = TeacherMonthlyReport::with('teacher:id,name,role,gender,phone')->findOrFail($id);

        if (!$user->isCoordinator()) {
            $visibleIds = $this->getVisibleTeacherIds($user);
            if (!in_array($report->teacher_id, $visibleIds)) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
        }

        return response()->json(['success' => true, 'data' => $this->formatMonthlyReport($report)]);
    }

    // GET /api/teacher-reports/monthly/teacher/{teacherId}
    public function monthlyByTeacher(Request $request, $teacherId)
    {
        $user = $request->user();

        if (!$user->isCoordinator()) {
            $visibleIds = $this->getVisibleTeacherIds($user);
            if (!in_array($teacherId, $visibleIds)) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
        }

        $reports = TeacherMonthlyReport::with('teacher:id,name,role,gender,phone')
            ->where('teacher_id', $teacherId)
            ->orderByDesc('year')->orderByDesc('month')->orderBy('period_start')
            ->get()
            ->map(fn($r) => $this->formatMonthlyReport($r));

        return response()->json(['success' => true, 'data' => $reports]);
    }

    // GET /api/teacher-reports/monthly/my-report
    public function monthlyMyReport(Request $request)
    {
        $reports = TeacherMonthlyReport::with('teacher:id,name,role,gender,phone')
            ->where('teacher_id', $request->user()->id)
            ->orderByDesc('year')->orderByDesc('month')->orderBy('period_start')
            ->get()
            ->map(fn($r) => $this->formatMonthlyReport($r));

        return response()->json(['success' => true, 'data' => $reports]);
    }

    // POST /api/teacher-reports/monthly/generate
    public function monthlyGenerate(Request $request)
    {
        $request->validate([
            'month'      => 'sometimes|integer|min:1|max:12',
            'year'       => 'sometimes|integer|min:2020',
            'teacher_id' => 'sometimes|exists:users,id',
        ]);

        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        if ($request->has('teacher_id')) {
            $report  = $this->service->generateMonthly($request->teacher_id, $month, $year);
            $results = [['teacher_id' => $request->teacher_id, 'status' => 'success', 'report_id' => $report->id]];
        } else {
            $results = $this->service->generateMonthlyForAll($month, $year);
        }

        return response()->json([
            'success' => true,
            'message' => 'Generate monthly report selesai.',
            'results' => $results,
        ]);
    }

    // POST /api/teacher-reports/monthly/stop-teacher
    public function stopTeacherPeriod(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:teacher_student_periods,id',
            'ended_at'  => 'required|date',
        ]);

        $period = TeacherStudentPeriod::findOrFail($request->period_id);

        if (!$period->is_active) {
            return response()->json(['success' => false, 'message' => 'Periode ini sudah tidak aktif.'], 422);
        }

        $report = $this->service->stopTeacher($period, $request->ended_at);

        return response()->json([
            'success' => true,
            'message' => 'Periode guru dihentikan dan laporan parsial berhasil dibuat.',
            'data'    => $this->formatMonthlyReport($report),
        ]);
    }

    // POST /api/teacher-reports/monthly/start-teacher
    public function startTeacherPeriod(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:students,id',
            'role_type'  => 'required|string|in:homeroom,shadow_pj,shadow_teacher,therapist',
            'started_at' => 'required|date',
        ]);

        $startedAt    = Carbon::parse($request->started_at);
        $academicYear = $this->service->getAcademicYear($startedAt->month, $startedAt->year);

        $period = $this->service->startPeriod(
            $request->teacher_id,
            $request->student_id,
            $request->role_type,
            $request->started_at,
            $academicYear
        );

        return response()->json([
            'success' => true,
            'message' => 'Periode guru baru berhasil dimulai.',
            'data'    => $period,
        ]);
    }

    // PUT /api/teacher-reports/monthly/{id}/recommendation
    public function monthlyRecommendation(Request $request, $id)
    {
        $request->validate([
            'coordinator_recommendation' => 'required|string',
            'performance_indicator'      => 'sometimes|in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ]);

        $report = TeacherMonthlyReport::findOrFail($id);
        $report->update($request->only('coordinator_recommendation', 'performance_indicator'));
        $report->load('teacher:id,name,role,gender,phone');

        return response()->json([
            'success' => true,
            'message' => 'Rekomendasi berhasil disimpan.',
            'data'    => $this->formatMonthlyReport($report),
        ]);
    }

    // ─── ANNUAL ───────────────────────────────────────────────────────────────

    // GET /api/teacher-reports/annual
    public function annualIndex(Request $request)
    {
        $user  = $request->user();
        $query = TeacherAnnualReport::with('teacher:id,name,role')
            ->orderByDesc('academic_year');

        if (!$user->isCoordinator()) {
            $visibleIds = $this->getVisibleTeacherIds($user);
            $query->whereIn('teacher_id', $visibleIds);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    // GET /api/teacher-reports/annual/{id}
    public function annualShow(Request $request, $id)
    {
        $user   = $request->user();
        $report = TeacherAnnualReport::with('teacher:id,name,role')->findOrFail($id);

        if (!$user->isCoordinator()) {
            $visibleIds = $this->getVisibleTeacherIds($user);
            if (!in_array($report->teacher_id, $visibleIds)) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
        }

        return response()->json(['success' => true, 'data' => $report]);
    }

    // GET /api/teacher-reports/annual/teacher/{teacherId}
    public function annualByTeacher(Request $request, $teacherId)
    {
        $user = $request->user();

        if (!$user->isCoordinator()) {
            $visibleIds = $this->getVisibleTeacherIds($user);
            if (!in_array($teacherId, $visibleIds)) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
        }

        $reports = TeacherAnnualReport::with('teacher:id,name,role')
            ->where('teacher_id', $teacherId)
            ->orderByDesc('academic_year')
            ->get();

        return response()->json(['success' => true, 'data' => $reports]);
    }

    // GET /api/teacher-reports/annual/my-report
    public function annualMyReport(Request $request)
    {
        $reports = TeacherAnnualReport::where('teacher_id', $request->user()->id)
            ->orderByDesc('academic_year')
            ->get();

        return response()->json(['success' => true, 'data' => $reports]);
    }

    // POST /api/teacher-reports/annual/generate
    public function annualGenerate(Request $request)
    {
        $request->validate([
            'academic_year' => 'sometimes|string|regex:/^\d{4}\/\d{4}$/',
            'teacher_id'    => 'sometimes|exists:users,id',
        ]);

        $academicYear = $request->input('academic_year', $this->service->getAcademicYear(now()->month, now()->year));

        if ($request->has('teacher_id')) {
            $report  = $this->service->generateAnnual($request->teacher_id, $academicYear);
            $results = [['teacher_id' => $request->teacher_id, 'status' => 'success', 'report_id' => $report->id]];
        } else {
            $results = $this->service->generateAnnualForAll($academicYear);
        }

        return response()->json([
            'success' => true,
            'message' => 'Generate annual report selesai.',
            'results' => $results,
        ]);
    }

    // PUT /api/teacher-reports/annual/{id}/recommendation
    public function annualRecommendation(Request $request, $id)
    {
        $request->validate([
            'coordinator_annual_recommendation' => 'required|string',
            'annual_performance_indicator'      => 'sometimes|in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ]);

        $report = TeacherAnnualReport::findOrFail($id);
        $report->update($request->only('coordinator_annual_recommendation', 'annual_performance_indicator'));

        return response()->json([
            'success' => true,
            'message' => 'Rekomendasi tahunan berhasil disimpan.',
            'data'    => $report,
        ]);
    }

    // ─── Format Monthly Report untuk Web ───────────────────────────────────────

    private function formatMonthlyReport(TeacherMonthlyReport $report): array
    {
        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $teacher = $report->teacher;
        $initials = $teacher
            ? strtoupper(collect(explode(' ', $teacher->name))->take(2)->map(fn($w) => substr($w, 0, 1))->implode(''))
            : '-';

        $performanceInfo = self::PERFORMANCE_LABELS[$report->performance_indicator] ?? self::PERFORMANCE_LABELS['tidak_tersedia'];

        $hasFeedback = !empty($report->coordinator_recommendation);

        return [
            'id'      => $report->id,
            'teacher' => [
                'id'       => $teacher->id ?? null,
                'name'     => $teacher->name ?? '-',
                'initials' => $initials,
                'role'     => $teacher->role ?? null,
                'role_label' => self::ROLE_LABELS[$teacher->role ?? ''] ?? ($teacher->role ?? '-'),
                'gender'   => $teacher->gender ?? null,
                'phone'    => $teacher->phone ?? null,
            ],
            'period' => [
                'month'          => $report->month,
                'year'           => $report->year,
                'label'          => ($bulanIndo[$report->month] ?? $report->month) . ' ' . $report->year,
                'academic_year'  => $report->academic_year,
                'period_start'   => $report->period_start,
                'period_end'     => $report->period_end,
                'is_partial'     => $report->is_partial,
                'partial_label'  => $report->is_partial ? 'Parsial' : 'Bulan Penuh',
            ],

            // ── Cards ringkasan (sesuai tampilan web) ──────────────────────
            'summary_cards' => [
                'completeness' => [
                    'label'      => 'Skor Kelengkapan',
                    'value'      => (float) $report->completeness_score,
                    'display'    => number_format((float) $report->completeness_score, 2) . '%',
                ],
                'feedback' => [
                    'label'   => 'Status Feedback',
                    'value'   => $hasFeedback,
                    'display' => $hasFeedback ? 'Sudah' : 'Belum',
                    'color'   => $hasFeedback ? '#52C41A' : '#FF4D4F',
                ],
                'performance' => [
                    'label'   => 'Indikator Performa',
                    'value'   => $report->performance_indicator,
                    'display' => strtoupper($performanceInfo['label']),
                    'color'   => $performanceInfo['color'],
                ],
            ],

            // ── Rekomendasi koordinator ─────────────────────────────────────
            'coordinator_recommendation' => [
                'has_recommendation' => $hasFeedback,
                'text'               => $report->coordinator_recommendation,
                'display'            => $report->coordinator_recommendation ?: 'Belum ada rekomendasi.',
            ],

            // ── Kehadiran & laporan ──────────────────────────────────────────
            'attendance' => [
                'total_teaching_days'   => $report->total_teaching_days,
                'total_reports_created' => $report->total_reports_created,
                'total_absent_days'     => $report->total_absent_days,
                'total_missing_days'    => $report->total_missing_days,
                'label'                 => "{$report->total_reports_created}/{$report->total_teaching_days} hari lapor",
            ],

            // ── Skor AI (observasi, analisis, solusi) ────────────────────────
            'ai_scores' => [
                'observation' => [
                    'label' => 'Skor Observasi',
                    'value' => (float) $report->observation_score,
                    'display' => number_format((float) $report->observation_score, 2) . ' / 5.00',
                ],
                'analysis' => [
                    'label' => 'Skor Analisis',
                    'value' => (float) $report->analysis_score,
                    'display' => number_format((float) $report->analysis_score, 2) . ' / 5.00',
                ],
                'solution' => [
                    'label' => 'Skor Solusi',
                    'value' => (float) $report->solution_score,
                    'display' => number_format((float) $report->solution_score, 2) . ' / 5.00',
                ],
            ],

            // ── Kualitas laporan ──────────────────────────────────────────────
            'report_quality' => [
                'avg_report_length'       => (float) $report->avg_report_length,
                'report_completeness_pct' => (float) $report->report_completeness_pct,
                'timeliness_score'        => (float) $report->timeliness_score,
                'weekly_consistency'      => (float) $report->weekly_consistency,
                'longest_streak'          => $report->longest_streak,
                'avg_fill_time_minutes'   => (float) $report->avg_fill_time_minutes,
                'avg_fill_time_label'     => $this->formatMinutes((float) $report->avg_fill_time_minutes),
            ],

            // ── Kondisi & mood siswa ──────────────────────────────────────────
            'student_wellbeing' => [
                'physical_health_pct'       => (float) $report->physical_health_pct,
                'mood_positive_pct'         => (float) $report->mood_positive_pct,
                'mood_consistency_pct'      => (float) $report->mood_consistency_pct,
                'total_challenges_recorded' => $report->total_challenges_recorded,
                'total_solutions_recorded'  => $report->total_solutions_recorded,
            ],

            // ── Worksheet ───────────────────────────────────────────────────
            'worksheet' => [
                'submission_pct'         => (float) $report->worksheet_submission_pct,
                'timeliness_pct'         => (float) $report->worksheet_timeliness_pct,
                'total_worksheets'       => $report->total_worksheets,
                'student_count'          => $report->worksheet_student_count,
                'per_student_avg'        => (float) $report->worksheet_per_student_avg,
            ],

            // ── Dokumentasi ─────────────────────────────────────────────────
            'documentation' => [
                'documentation_pct'   => (float) $report->documentation_pct,
                'docs_per_report_avg' => (float) $report->docs_per_report_avg,
                'documented_weeks'    => $report->documented_weeks,
            ],

            // ── Siswa yang ditangani ────────────────────────────────────────
            'students' => [
                'active_student_count'          => $report->active_student_count,
                'students_no_report_this_week'  => $report->students_no_report_this_week,
                'student_positive_progress_pct' => (float) $report->student_positive_progress_pct,
                'reports_per_student_avg'       => (float) $report->reports_per_student_avg,
            ],

            // ── AI insight ──────────────────────────────────────────────────
            'ai_insight' => [
                'summary'           => $report->ai_performance_summary,
                'improvement_areas' => $report->ai_improvement_areas,
            ],

            'meta' => [
                'status'       => $report->status,
                'generated_at' => $report->generated_at,
                'created_at'   => $report->created_at,
                'updated_at'   => $report->updated_at,
            ],
        ];
    }

    private function formatMinutes(?float $minutes): string
    {
        if (!$minutes) return '-';

        $hours = floor($minutes / 60);
        $mins  = round($minutes % 60);

        if ($hours > 0) {
            return "{$hours} jam {$mins} menit";
        }

        return "{$mins} menit";
    }

    // ─── Helper: ambil teacher_id yang boleh dilihat ──────────────────────────

    private function getVisibleTeacherIds($user): array
    {
        $ids = [$user->id];

        $academicYear = $this->service->getAcademicYear(now()->month, now()->year);

        if (in_array($user->role, ['therapist_homeroom', 'shadow_pj'])) {
            $myStudentIds = TeacherStudentPeriod::where('teacher_id', $user->id)
                ->where('academic_year', $academicYear)
                ->pluck('student_id');

            $peerIds = TeacherStudentPeriod::whereIn('student_id', $myStudentIds)
                ->where('academic_year', $academicYear)
                ->pluck('teacher_id')
                ->toArray();

            $ids = array_unique(array_merge($ids, $peerIds));
        }

        return $ids;
    }
}
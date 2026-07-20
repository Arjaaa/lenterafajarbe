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
        $report = TeacherMonthlyReport::with('teacher:id,name,role')->findOrFail($id);

        if (!$user->isCoordinator()) {
            $visibleIds = $this->getVisibleTeacherIds($user);
            if (!in_array($report->teacher_id, $visibleIds)) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
        }

        return response()->json(['success' => true, 'data' => $report]);
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

        $reports = TeacherMonthlyReport::with('teacher:id,name,role')
            ->where('teacher_id', $teacherId)
            ->orderByDesc('year')->orderByDesc('month')->orderBy('period_start')
            ->get();

        return response()->json(['success' => true, 'data' => $reports]);
    }

    // GET /api/teacher-reports/monthly/my-report
    public function monthlyMyReport(Request $request)
    {
        $reports = TeacherMonthlyReport::where('teacher_id', $request->user()->id)
            ->orderByDesc('year')->orderByDesc('month')->orderBy('period_start')
            ->get();

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
    // Dipanggil saat guru di-nonaktifkan/diberhentikan/pindah di tengah bulan.
    // Menutup periode + langsung generate partial report sampai tanggal berhenti.
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
            'data'    => $report,
        ]);
    }

    // POST /api/teacher-reports/monthly/start-teacher
    // Dipanggil saat guru pengganti mulai bertugas (mis. di tengah bulan setelah PHK).
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

        return response()->json([
            'success' => true,
            'message' => 'Rekomendasi berhasil disimpan.',
            'data'    => $report,
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

    // ─── Helper: ambil teacher_id yang boleh dilihat ──────────────────────────
    // Catatan: pakai kondisi where terpisah (bukan filter ->where('is_active', true)
    // tunggal di awal) supaya guru yang baru di-stop (is_active=false tapi masih
    // relevan di bulan berjalan) tidak langsung kehilangan visibilitas ke laporan
    // peer-nya untuk periode yang baru saja berakhir.

    private function getVisibleTeacherIds($user): array
    {
        $ids = [$user->id]; // selalu bisa lihat diri sendiri

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
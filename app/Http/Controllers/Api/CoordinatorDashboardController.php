<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\ShadowGroup;
use App\Models\OneOnOneGroup;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\MonthlyReport;

class CoordinatorDashboardController extends Controller
{
    // GET /api/coordinator/dashboard
public function index()
{
    // ── Total Pengguna ────────────────────────────────────────────────────
    $totalAnak       = Student::count();
    $totalOrangTua   = User::where('role', 'parent')->count();
    $totalWaliKelas  = User::where('role', 'therapist_homeroom')->count();
    $totalTerapis1on1 = User::where('role', 'therapist')->count();
    $totalShadow     = User::whereIn('role', ['shadow_pj', 'shadow_teacher'])->count();
    $totalKelas      = ClassRoom::count();
    $totalGroup      = ShadowGroup::count() + OneOnOneGroup::count();

    // ── Sebaran Penempatan ────────────────────────────────────────────────
    $kelasReguler  = ClassRoom::count(); // ✅ jumlah kelas, bukan jumlah siswa
    $groupShadow   = ShadowGroup::count();
    $sesiOneOnOne  = OneOnOneGroup::count();

    return response()->json([
        'success' => true,
        'data'    => [
            'summary' => [
                [
                    'title'    => 'Total Siswa',
                    'value'    => $totalAnak,
                    'key'      => 'total_siswa',
                    'subtitle' => 'Terdaftar',
                ],
                [
                    'title'    => 'Total Wali Kelas',
                    'value'    => $totalWaliKelas,
                    'key'      => 'total_wali_kelas',
                    'subtitle' => 'Siap Bertugas',
                ],
                [
                    'title'    => 'Total Terapis 1 on 1',
                    'value'    => $totalTerapis1on1,
                    'key'      => 'total_terapis_1on1',
                    'subtitle' => 'Siap Bertugas',
                ],
                [
                    'title'    => 'Total Shadow Teacher',
                    'value'    => $totalShadow,
                    'key'      => 'total_shadow_teacher',
                    'subtitle' => 'Siap Bertugas',
                ],
            ],
            'sebaran_penempatan' => [
                [
                    'title' => 'Kelas Terapis',
                    'value' => $kelasReguler,
                    'key'   => 'kelas_terapis',
                ],
                [
                    'title' => 'Sesi 1 on 1',
                    'value' => $sesiOneOnOne,
                    'key'   => 'sesi_one_on_one',
                ],
                [
                    'title' => 'Group Shadow Teacher',
                    'value' => $groupShadow,
                    'key'   => 'group_shadow_teacher',
                ],
            ],
        ],
    ]);
}

    // GET /api/coordinator/worksheets
    public function worksheets(Request $request)
    {
        $today     = now()->toDateString();
        $thisMonth = now()->month;
        $thisYear  = now()->year;

        // ── Stats cards ───────────────────────────────────────────────────────
        $totalStudents = Student::count();

        // Total worksheet semua waktu
        $totalWorksheets = \App\Models\Worksheet::count();

        // Worksheet bulan ini
        $thisMonthTotal     = \App\Models\Worksheet::whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->count();
        $thisMonthSubmitted = \App\Models\Worksheet::whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->where('status', 'submitted')->count();

        // Laporan harian hari ini
        $todayReports = DailyReport::whereDate('date', $today)->count();

        // ── Query tabel ───────────────────────────────────────────────────────
        $query = \App\Models\Worksheet::with([
            'uploader:id,name,role',
            'student:id,name',
            'student.classes:id,name',
        ])->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter bulan & tahun
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', $request->year);
        }

        // Search judul, keterangan, nama pembuat, nama siswa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('uploader', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('student', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // Pagination
        $perPage    = $request->input('per_page', 15);
        $worksheets = $query->paginate($perPage);

        $data = $worksheets->map(function ($ws) {
            return [
                'id'          => $ws->id,
                'file_type'   => $ws->file_type,
                'file_url'    => $ws->file_url,
                'title'       => $ws->title,
                'description' => $ws->description,
                'status'      => $ws->status,
                'pembuat' => [
                    'id'   => $ws->uploader?->id,
                    'name' => $ws->uploader?->name,
                    'role' => $ws->uploader?->role,
                ],
                'student' => [
                    'id'    => $ws->student?->id,
                    'name'  => $ws->student?->name,
                    'class' => $ws->student?->classes->first()?->name,
                ],
                'created_at' => $ws->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'stats'   => [
                'total_worksheets'        => $totalWorksheets,
                'this_month_submitted'    => $thisMonthSubmitted,
                'this_month_total'        => $thisMonthTotal,
                'today_reports'           => $todayReports,
                'today_total'             => $totalStudents,
            ],
            'data'       => $data,
            'pagination' => [
                'current_page' => $worksheets->currentPage(),
                'last_page'    => $worksheets->lastPage(),
                'per_page'     => $worksheets->perPage(),
                'total'        => $worksheets->total(),
            ],
        ]);
    }

    // GET /api/coordinator/teachers
    public function allTeachers(Request $request)
    {
        // Stats cards
        $totalWaliKelas   = User::where("role", "therapist_homeroom")->count();
        $totalTerapis1on1 = User::where("role", "therapist")->count();
        $totalShadow      = User::whereIn("role", ["shadow_pj", "shadow_teacher"])->count();
        $totalBelumDitugaskan = User::whereNull('role')->count();

        // Query tabel
        $query = User::where(function ($q) {
    $q->whereIn('role', ['shadow_pj', 'shadow_teacher', 'therapist_homeroom', 'therapist'])
      ->orWhereNull('role');
})->latest();

        if ($request->filled("role")) {
    if ($request->role === 'unassigned') {
        $query->whereNull('role');
    } else {
        $query->where("role", $request->role);
    }
}
        if ($request->filled("search")) { $search = $request->search; $query->where(function ($q) use ($search) { $q->where("name", "like", "%{$search}%")->orWhere("email", "like", "%{$search}%"); }); }

        $perPage  = $request->input("per_page", 15);
        $teachers = $query->paginate($perPage);

        $roleLabels = ["therapist_homeroom" => "Wali Kelas", "therapist" => "Terapis 1 on 1", "shadow_pj" => "Shadow PJ", "shadow_teacher" => "Shadow Teacher", "coordinator_main" => "Koordinator Utama", "coordinator_therapist" => "Koordinator Terapis", "coordinator_shadow" => "Koordinator Shadow", "coordinator_wil" => "Koordinator Wilayah"];

        // NOTE: kolom "photo" TIDAK ADA di tabel users, jadi dihapus dari select & response.
        // Kalau nanti mau ada foto guru, tambah migration kolom photo dulu.
        $data = $teachers->map(function ($u) use ($roleLabels) {
            return [
                "id"         => $u->id,
                "name"       => $u->name,
                "email"      => $u->email,
                "role"       => $u->role,
                "role_label" => $u->role ? ($roleLabels[$u->role] ?? $u->role) : 'Belum Ditugaskan',
                "gender"     => $u->gender,
                "phone"      => $u->phone,
                "created_at" => $u->created_at,
            ];
        });

        return response()->json([
            "success" => true,
            "stats" => [
                "total_wali_kelas"   => $totalWaliKelas,
                "total_terapis_1on1" => $totalTerapis1on1,
                "total_shadow"       => $totalShadow,
                "total_belum_ditugaskan" => $totalBelumDitugaskan,
            ],
            "data" => $data,
            "pagination" => [
                "current_page" => $teachers->currentPage(),
                "last_page"    => $teachers->lastPage(),
                "per_page"     => $teachers->perPage(),
                "total"        => $teachers->total(),
            ],
        ]);
    }

    // GET /api/coordinator/teacher-reports
    public function teacherReports(Request $request)
    {
        $thisMonth = now()->month;
        $thisYear  = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        // ── Stats cards ───────────────────────────────────────────────────────
        $totalGuru = User::whereIn('role', [
            'shadow_pj', 'shadow_teacher',
            'therapist_homeroom', 'therapist',
            'coordinator_main', 'coordinator_therapist',
            'coordinator_shadow', 'coordinator_wil',
        ])->count();

        // Total seluruh rapor guru
        $totalReports = \App\Models\TeacherMonthlyReport::where('status', 'generated')->count();

        // Rapor bulan lalu
        $lastMonthTotal     = \App\Models\TeacherMonthlyReport::where('month', $lastMonth)->where('year', $lastMonthYear)->where('status', 'generated')->count();
        $lastMonthFeedback  = \App\Models\TeacherMonthlyReport::where('month', $lastMonth)->where('year', $lastMonthYear)->where('status', 'generated')->whereNotNull('coordinator_recommendation')->count();

        // Rapor sudah diberi feedback (bulan ini)
        $thisMonthTotal    = \App\Models\TeacherMonthlyReport::where('month', $thisMonth)->where('year', $thisYear)->where('status', 'generated')->count();
        $thisMonthFeedback = \App\Models\TeacherMonthlyReport::where('month', $thisMonth)->where('year', $thisYear)->where('status', 'generated')->whereNotNull('coordinator_recommendation')->count();

        // ── Query tabel ───────────────────────────────────────────────────────
        // NOTE: "photo" dihapus dari select relasi teacher karena kolom itu tidak ada di tabel users.
        $query = \App\Models\TeacherMonthlyReport::with([
            'teacher:id,name,role,gender,phone',
        ])
        ->where('status', 'generated')
        ->orderByDesc('year')
        ->orderByDesc('month');

        // Filter bulan & tahun
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter sudah/belum feedback
        if ($request->filled('feedback')) {
            if ($request->feedback === 'given') {
                $query->whereNotNull('coordinator_recommendation');
            } else {
                $query->whereNull('coordinator_recommendation');
            }
        }

        // Search nama guru
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('teacher', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $reports = $query->paginate($perPage);

        $data = $reports->map(function ($r) {
            return [
                'id'    => $r->id,
                'month' => $r->month,
                'year'  => $r->year,
                'teacher' => [
                    'id'     => $r->teacher?->id,
                    'name'   => $r->teacher?->name,
                    'role'   => $r->teacher?->role,
                    'gender' => $r->teacher?->gender,
                    'phone'  => $r->teacher?->phone,
                ],
                'performance_indicator'      => $r->performance_indicator,
                'completeness_score'         => $r->completeness_score,
                'has_feedback'               => !is_null($r->coordinator_recommendation),
                'coordinator_recommendation' => $r->coordinator_recommendation,
                'generated_at'               => $r->generated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'stats'   => [
                'feedback_given'       => $thisMonthFeedback,
                'feedback_total'       => $thisMonthTotal,
                'last_month_reports'   => $lastMonthTotal,
                'last_month_feedback'  => $lastMonthFeedback,
                'total_reports'        => $totalReports,
                'total_guru'           => $totalGuru,
            ],
            'data'       => $data,
            'pagination' => [
                'current_page' => $reports->currentPage(),
                'last_page'    => $reports->lastPage(),
                'per_page'     => $reports->perPage(),
                'total'        => $reports->total(),
            ],
        ]);
    }
    // GET /api/coordinator/teacher-reports/{id}
    public function teacherReportShow($id)
    {
        $report = \App\Models\TeacherMonthlyReport::with('teacher:id,name,role,gender,phone')->findOrFail($id);

        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $performanceLabel = [
            'sangat_baik'    => 'Sangat Baik',
            'baik'           => 'Baik',
            'cukup'          => 'Cukup',
            'kurang'         => 'Kurang',
            'sangat_kurang'  => 'Sangat Kurang',
            'tidak_tersedia' => 'Tidak Tersedia',
        ];

        $roleLabel = [
            'therapist_homeroom' => 'Wali Kelas',
            'therapist'          => 'Terapis 1 on 1',
            'shadow_pj'          => 'Shadow PJ',
            'shadow_teacher'     => 'Shadow Teacher',
        ];

        $name   = $report->teacher?->name ?? '-';
        $parts  = explode(' ', $name);
        $avatar = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));

        return response()->json([
            'success' => true,
            'data'    => [
                'id'      => $report->id,
                'teacher' => [
                    'id'         => $report->teacher?->id,
                    'name'       => $name,
                    'avatar'     => $avatar,
                    'role'       => $report->teacher?->role,
                    'role_label' => $roleLabel[$report->teacher?->role] ?? '-',
                    'phone'      => $report->teacher?->phone,
                ],
                'period' => [
                    'month'        => $report->month,
                    'year'         => $report->year,
                    'label'        => ($bulanIndo[$report->month] ?? $report->month) . ' ' . $report->year,
                    'is_partial'   => $report->is_partial,
                    'period_start' => $report->period_start,
                    'period_end'   => $report->period_end,
                ],
                'stats' => [
                    'hari_mengajar'            => $report->total_teaching_days,
                    'skor_kelengkapan'         => (float) $report->completeness_score,
                    'total_laporan'            => $report->total_reports_created,
                    'indikator_performa'       => $report->performance_indicator,
                    'indikator_performa_label' => $performanceLabel[$report->performance_indicator] ?? '-',
                    'ketepatan_waktu'          => (float) $report->timeliness_score,
                    'konsistensi_mingguan'     => (float) $report->weekly_consistency,
                    'dokumentasi'              => (float) $report->documentation_pct,
                    'siswa_progres_positif'    => (float) $report->student_positive_progress_pct,
                ],
                'detail_lain' => [
                    'total_absent_days'        => $report->total_absent_days,
                    'total_missing_days'       => $report->total_missing_days,
                    'avg_report_length'        => (float) $report->avg_report_length,
                    'longest_streak'           => $report->longest_streak,
                    'avg_fill_time_minutes'    => $report->avg_fill_time_minutes,
                    'physical_health_pct'      => (float) $report->physical_health_pct,
                    'mood_positive_pct'        => (float) $report->mood_positive_pct,
                    'total_worksheets'         => $report->total_worksheets,
                    'worksheet_submission_pct' => (float) $report->worksheet_submission_pct,
                    'active_student_count'     => $report->active_student_count,
                ],
                'ai_insight' => [
                    'summary'           => $report->ai_performance_summary,
                    'improvement_areas' => $report->ai_improvement_areas ?? [],
                    'observation_score' => $report->observation_score,
                    'analysis_score'    => $report->analysis_score,
                    'solution_score'    => $report->solution_score,
                ],
                'coordinator_recommendation' => $report->coordinator_recommendation,
                'status'       => $report->status,
                'generated_at' => $report->generated_at,
            ],
        ]);
    }

    // GET /api/coordinator/daily-reports
    public function dailyReports(Request $request)
    {
        $today      = now()->toDateString();
        $thisMonth  = now()->format('Y-m');
        $lastMonth  = now()->subMonth()->format('Y-m');

        // ── Stats cards ───────────────────────────────────────────────────────
        $totalStudents = Student::count();

        // Laporan hari ini
        $todayReports = DailyReport::whereDate('date', $today)->count();

        // Laporan bulan lalu
        $lastMonthReports = DailyReport::whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$lastMonth])->count();

        // Total seluruh laporan
        $totalReports = DailyReport::count();

        // ── Query tabel ───────────────────────────────────────────────────────
        $query = DailyReport::with([
            'student:id,name,photo',
            'detail:id,daily_report_id,activity_notes',
            'shadowTeacher:id,name,role',
            'therapist:id,name,role',
        ])->latest('created_at');

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Filter bulan
        if ($request->filled('month')) {
            $query->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$request->month]);
        }

        // Filter student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter attendance_status
        if ($request->filled('attendance_status')) {
            $query->where('attendance_status', $request->attendance_status);
        }

        // Search by nama siswa atau nama guru
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('shadowTeacher', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('therapist', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $reports = $query->paginate($perPage);

        // Format data tabel
        $data = $reports->map(function ($r) {
            $pembuat = $r->shadowTeacher ?? $r->therapist;
            return [
                'id'                => $r->id,
                'student' => [
                    'id'    => $r->student?->id,
                    'name'  => $r->student?->name,
                    'photo' => $r->student?->photo,
                ],
                'activity_notes'    => $r->detail?->activity_notes,
                'attendance_status' => $r->attendance_status,
                'pembuat_laporan' => [
                    'id'   => $pembuat?->id,
                    'name' => $pembuat?->name,
                    'role' => $pembuat?->role,
                ],
                'date'       => $r->date,
                'created_at' => $r->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'stats'   => [
                'today_reports'      => $todayReports,
                'today_total'        => $totalStudents,
                'last_month_reports' => $lastMonthReports,
                'last_month_total'   => $totalStudents,
                'total_reports'      => $totalReports,
                'total_students'     => $totalStudents,
            ],
            'data'       => $data,
            'pagination' => [
                'current_page' => $reports->currentPage(),
                'last_page'    => $reports->lastPage(),
                'per_page'     => $reports->perPage(),
                'total'        => $reports->total(),
            ],
        ]);
    }

    // GET /api/coordinator/students/{studentId}/documentation
    public function studentDocumentation(Request $request, $studentId)
    {
        $student = Student::select('id', 'name', 'photo')->findOrFail($studentId);

        $date = $request->filled('date') ? $request->date : now()->toDateString();

        $report = DailyReport::with('detail')
            ->where('student_id', $studentId)
            ->whereDate('date', $date)
            ->first();

        // ── Gabungkan semua foto jadi 1 list flat ───────────────────────────
        $photos = collect();

        if ($report && $report->detail) {
            $detail = $report->detail;

            foreach (($detail->photo_physical ?? []) as $path) {
                $photos->push([
                    'type'       => 'physical',
                    'type_label' => 'Kondisi Fisik',
                    'photo_url'  => $this->photoUrl($path),
                    'note'       => $detail->physical_condition_arrival_label,
                ]);
            }

            foreach (($detail->photo_activity ?? []) as $path) {
                $photos->push([
                    'type'       => 'activity',
                    'type_label' => 'Kegiatan',
                    'photo_url'  => $this->photoUrl($path),
                    'note'       => $detail->activity_notes,
                ]);
            }

            foreach (($detail->photo_other ?? []) as $path) {
                $photos->push([
                    'type'       => 'other',
                    'type_label' => 'Lainnya',
                    'photo_url'  => $this->photoUrl($path),
                    'note'       => null,
                ]);
            }
        }

        $photos = $photos->values();

        // ── Pagination manual: 1 foto = 1 halaman ───────────────────────────
        $perPage  = 1;
        $total    = $photos->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = (int) $request->input('page', 1);
        $page     = min(max($page, 1), $lastPage);

        $currentPhoto = $photos->slice(($page - 1) * $perPage, $perPage)->first();

        return response()->json([
            'success' => true,
            'student' => [
                'id'    => $student->id,
                'name'  => $student->name,
                'photo' => $student->photo,
            ],
            'date'              => $date,
            'date_label'        => Carbon::parse($date)->translatedFormat('l, d-m-Y'),
            'attendance_status' => $report->attendance_status ?? null,
            'data'              => $currentPhoto, // null kalau tidak ada foto hari itu
            'pagination'        => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'per_page'     => $perPage,
                'total'        => $total,
            ],
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────────────────
    private function photoUrl(?string $path): ?string
    {
        if (!$path) return null;
        return asset('storage/' . $path);
    }
    // GET /api/coordinator/monthly-reports
public function allMonthlyReports(Request $request)
{
    $thisMonth     = now()->month;
    $thisYear      = now()->year;
    $lastMonth     = now()->subMonth()->month;
    $lastMonthYear = now()->subMonth()->year;

    $totalAnak         = Student::count();
    $totalReports      = MonthlyReport::where('status', 'generated')->count();
    $thisMonthTotal    = MonthlyReport::where('month', $thisMonth)->where('year', $thisYear)->where('status', 'generated')->count();
    $thisMonthFeedback = MonthlyReport::where('month', $thisMonth)->where('year', $thisYear)->where('status', 'generated')->whereNotNull('coordinator_note')->count();
    $lastMonthTotal    = MonthlyReport::where('month', $lastMonth)->where('year', $lastMonthYear)->where('status', 'generated')->count();
    $lastMonthFeedback = MonthlyReport::where('month', $lastMonth)->where('year', $lastMonthYear)->where('status', 'generated')->whereNotNull('coordinator_note')->count();

    $query = MonthlyReport::with('student:id,name,photo')
        ->where('status', 'generated')
        ->orderByDesc('year')
        ->orderByDesc('month');

    if ($request->filled('student_id')) { $query->where('student_id', $request->student_id); }
    if ($request->filled('month'))      { $query->where('month', $request->month); }
    if ($request->filled('year'))       { $query->where('year', $request->year); }
    if ($request->filled('feedback')) {
        if ($request->feedback === 'given') {
            $query->whereNotNull('coordinator_note');
        } else {
            $query->whereNull('coordinator_note');
        }
    }
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%"));
    }

    $perPage = $request->input('per_page', 15);
    $reports = $query->paginate($perPage);

    $bulanIndo = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
    ];

    $data = $reports->map(function ($r) use ($bulanIndo) {
        $overallDominant = collect($r->overall_score_stats ?? [])
            ->sortByDesc(fn($v) => $v['count'] ?? 0)
            ->keys()
            ->first();

        // Ambil semua guru aktif yang mengajar siswa ini
        $teachers = \App\Models\TeacherStudentPeriod::with('teacher:id,name,role')
            ->where('student_id', $r->student_id)
            ->where('is_active', true)
            ->get()
            ->map(fn($p) => [
                'id'        => $p->teacher?->id,
                'name'      => $p->teacher?->name,
                'role'      => $p->teacher?->role,
                'role_type' => $p->role_type,
            ])
            ->filter(fn($t) => $t['id'])
            ->values();

        return [
            'id'               => $r->id,
            'month'            => $r->month,
            'year'             => $r->year,
            'period_label'     => ($bulanIndo[$r->month] ?? $r->month) . ' ' . $r->year,
            'student'          => [
                'id'    => $r->student?->id,
                'name'  => $r->student?->name,
                'photo' => $r->student?->photo,
            ],
            'teachers'         => $teachers,
            'total_reports'    => $r->total_reports,
            'mood_arrival_avg' => (float) $r->mood_arrival_avg,
            'mood_end_avg'     => (float) $r->mood_end_avg,
            'overall_score'    => $overallDominant,
            'has_feedback'     => !is_null($r->coordinator_note),
            'coordinator_note' => $r->coordinator_note,
            'generated_at'     => $r->generated_at,
        ];
    });

    return response()->json([
        'success' => true,
        'stats'   => [
            'feedback_given'      => $thisMonthFeedback,
            'feedback_total'      => $thisMonthTotal,
            'last_month_reports'  => $lastMonthTotal,
            'last_month_feedback' => $lastMonthFeedback,
            'total_reports'       => $totalReports,
            'total_anak'          => $totalAnak,
        ],
        'data'       => $data,
        'pagination' => [
            'current_page' => $reports->currentPage(),
            'last_page'    => $reports->lastPage(),
            'per_page'     => $reports->perPage(),
            'total'        => $reports->total(),
        ],
    ]);
}
}
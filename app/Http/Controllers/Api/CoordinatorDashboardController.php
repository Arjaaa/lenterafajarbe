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

class CoordinatorDashboardController extends Controller
{
    // GET /api/coordinator/dashboard
    public function index()
    {
        // ── Total Pengguna ────────────────────────────────────────────────────
        $totalAnak      = Student::count();
        $totalOrangTua  = User::where('role', 'parent')->count();
        $totalGuru      = User::whereIn('role', [
            'shadow_pj', 'shadow_teacher',
            'therapist_homeroom', 'therapist',
            'coordinator_main', 'coordinator_therapist',
            'coordinator_shadow', 'coordinator_wil',
        ])->count();
        $totalKelas     = ClassRoom::count();
        $totalGroup     = ShadowGroup::count() + OneOnOneGroup::count();

        // ── Sebaran Penempatan ────────────────────────────────────────────────
        $kelasReguler  = ClassRoom::withCount('students')->get()->sum('students_count');
        $groupShadow   = ShadowGroup::count();
        $sesiOneOnOne  = OneOnOneGroup::count();

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    [
                        'title'    => 'Total Anak',
                        'value'    => $totalAnak,
                        'key'      => 'total_anak',
                        'subtitle' => 'Terdaftar',
                    ],
                    [
                        'title'    => 'Total Orang Tua',
                        'value'    => $totalOrangTua,
                        'key'      => 'total_orang_tua',
                        'subtitle' => 'Akun Aktif',
                    ],
                    [
                        'title'    => 'Total Guru & Terapis',
                        'value'    => $totalGuru,
                        'key'      => 'total_guru',
                        'subtitle' => 'Siap Bertugas',
                    ],
                    [
                        'title'    => 'Total Kelas & Grup',
                        'value'    => $totalKelas + $totalGroup,
                        'key'      => 'total_kelas_grup',
                        'subtitle' => 'Sedang Berjalan',
                    ],
                ],
                'sebaran_penempatan' => [
                    [
                        'title' => 'Kelas Reguler',
                        'value' => $kelasReguler,
                        'key'   => 'kelas_reguler',
                    ],
                    [
                        'title' => 'Group Shadow',
                        'value' => $groupShadow,
                        'key'   => 'group_shadow',
                    ],
                    [
                        'title' => 'Sesi 1 on 1',
                        'value' => $sesiOneOnOne,
                        'key'   => 'sesi_one_on_one',
                    ],
                ],
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
}
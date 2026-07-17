<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\DailyReport;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\ShadowGroup;
use App\Models\OneOnOneGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private array $avatarColors = [
        '#7C6EF5', '#F5A623', '#52C41A', '#4A90E2',
        '#F5222D', '#13C2C2', '#EB2F96', '#FA8C16',
    ];

    public function index(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today();
        $week  = Carbon::now()->startOfWeek();

        // ─── 0. ANAK YANG JADI TANGGUNG JAWAB GURU INI ────────────────────────
        // (dipakai bareng oleh statistics, sesi_laporan_hari_ini, & daftar_anak
        // biar semua angka konsisten — sebelumnya statistics pakai filterByRole
        // yang beda logic dari getMyStudentIds, jadi angkanya suka nggak sinkron)
        $studentIds = $this->getMyStudentIds($user);

        // ─── 1. STATS ────────────────────────────────────────────────────────

        $baseQuery = DailyReport::whereIn('student_id', $studentIds)
            ->whereDate('date', $today);

        $todayReports    = (clone $baseQuery)->count();
        $todayAttendance = (clone $baseQuery)->where('attendance_status', 'hadir')->distinct('student_id')->count('student_id');
        $todayActivities = (clone $baseQuery)
            ->whereHas('detail', fn($q) => $q->whereNotNull('activity_notes')
                ->where('activity_notes', '!=', ''))
            ->count();
        $weeklyReports = DailyReport::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$week, Carbon::now()])
            ->count();

        // ─── 2. ANNOUNCEMENT ─────────────────────────────────────────────────

        $announcement = Announcement::where('is_active', true)
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $today))
            ->orderByDesc('created_at')
            ->first();

        // ─── 3. DOKUMENTASI TERBARU ──────────────────────────────────────────

        $docsReports = DailyReport::whereIn('student_id', $studentIds)
            ->whereDate('date', $today)
            ->with('detail:id,daily_report_id,photo_activity,photo_physical,photo_other')
            ->get();

        $images = $docsReports->flatMap(function ($report) {
            $d = $report->detail;
            if (!$d) return [];
            return array_filter([
                $d->photo_activity ?: null,
                $d->photo_physical ?: null,
                $d->photo_other    ?: null,
            ]);
        })->values()->take(10)->toArray();

        $latestDocumentations = [];
        if (!empty($images)) {
            $latestDocumentations[] = [
                'id'     => 1,
                'date'   => $today->toDateString(),
                'images' => $images,
            ];
        }

        // ─── 4. LAPORAN TERKINI ───────────────────────────────────────────────

        $latestReports = DailyReport::whereIn('student_id', $studentIds)
            ->whereDate('date', $today)
            ->with([
                'student:id,name',
                'detail:id,daily_report_id,activity_notes,behavior',
            ])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($r) => [
                'id'             => $r->id,
                'student_name'   => $r->student->name ?? '-',
                'student_avatar' => strtoupper(substr($r->student->name ?? 'X', 0, 1)),
                'summary'        => $this->buildStatus($r->detail),
                'created_at'     => $r->created_at->format('Y-m-d H:i:s'),
            ]);

        // ─── 5. TEACHER INFO ─────────────────────────────────────────────────

        $initials = collect(explode(' ', $user->name))
            ->take(2)
            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
            ->implode('');

        $roleLabel = [
            'coordinator_main'       => 'Koordinator Utama',
            'coordinator_therapist'  => 'Koordinator Terapis',
            'coordinator_shadow'     => 'Koordinator Shadow',
            'coordinator_wil'        => 'Koordinator Wilayah',
            'shadow_pj'              => 'Shadow PJ',
            'shadow_teacher'         => 'Guru Shadow',
            'therapist_homeroom'     => 'Guru Terapis',
            'therapist'              => 'Terapis',
            'parent'                 => 'Orang Tua',
        ][$user->role] ?? $user->role;

        // ─── 6. DAFTAR ANAK + SESI LAPORAN HARI INI ───────────────────────────

        $daftarAnak = $this->buildDaftarAnak($studentIds, $today);

        $sudahLaporCount = $daftarAnak->where('report_status', 'sudah')->count();
        $totalAnakCount  = $daftarAnak->count();
        $belumDiisiCount = $daftarAnak->where('report_status', 'belum')->count();
        $progressPersen  = $totalAnakCount > 0 ? round(($sudahLaporCount / $totalAnakCount) * 100) : 0;

        // ─── RESPONSE ────────────────────────────────────────────────────────

        return response()->json([
            'success' => true,
            'message' => 'Dashboard fetched successfully.',
            'data'    => [
                'teacher' => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'role'        => $roleLabel,
                    'school_name' => $user->school_name ?? 'Lentera Fajar Indonesia',
                    'avatar'      => [
                        'type'  => 'initial',
                        'value' => $initials,
                    ],
                ],
                'statistics' => [
                    ['title' => 'Laporan hari ini',   'value' => $todayReports,    'key' => 'today_reports'],
                    ['title' => 'Kegiatan hari ini',  'value' => $todayActivities, 'key' => 'today_activities'],
                    ['title' => 'Hadir hari ini',     'value' => $todayAttendance, 'key' => 'today_attendance'],
                    ['title' => 'Laporan minggu ini', 'value' => $weeklyReports,   'key' => 'weekly_reports'],
                ],
                'announcement' => $announcement ? [
                    'id'          => $announcement->id,
                    'title'       => $announcement->title,
                    'description' => $announcement->description,
                    'start_date'  => $announcement->start_date?->toDateString(),
                    'type'        => $announcement->type,
                ] : null,
                'quick_access' => [
                    [
                        'title'       => 'Kelas & Anak',
                        'description' => 'Kelola kelas dan buat laporan',
                        'icon'        => 'book',
                        'route'       => '/teacher/classes',
                    ],
                    [
                        'title'       => 'Worksheet',
                        'description' => 'Kelola worksheet siswa',
                        'icon'        => 'emoji',
                        'route'       => '/teacher/worksheets',
                    ],
                    [
                        'title'       => 'Laporan Guru',
                        'description' => 'Lihat catatan & performa',
                        'icon'        => 'chart',
                        'route'       => '/teacher/reports',
                    ],
                ],
                // ── Sesi laporan hari ini (buat card di dashboard) ────────
                'sesi_laporan_hari_ini' => [
                    'tanggal'         => $today->translatedFormat('l, d M Y'),
                    'sudah_lapor'     => $sudahLaporCount,
                    'total_siswa'     => $totalAnakCount,
                    'belum_diisi'     => $belumDiisiCount,
                    'progress_persen' => $progressPersen,
                    'label'           => "{$sudahLaporCount}/{$totalAnakCount}",
                ],
                // ── Daftar anak (buat expand-list di bawah card) ──────────
                'daftar_anak'           => $daftarAnak,
                'latest_documentations' => $latestDocumentations,
                'latest_reports'        => $latestReports,
                'notifications_count'   => 0,
            ],
        ]);
    }

    private function buildStatus(?object $detail): string
    {
        if (!$detail) return 'Laporan belum lengkap';

        if ($detail->activity_notes) {
            return Str::limit($detail->activity_notes, 40);
        }

        return match ($detail->behavior) {
            'kooperatif'         => 'Aktif dan kooperatif hari ini',
            'mudah_terdistraksi' => 'Perlu perhatian lebih hari ini',
            'agresif'            => 'Butuh pendampingan ekstra',
            'pasif'              => 'Kurang aktif hari ini',
            default              => 'Aktif mengikuti kegiatan hari ini',
        };
    }

    // ── Ambil semua ID anak yang jadi tanggung jawab guru/terapis ini ────────
    // (reuse logic yang sama dengan DailyReportController::myStudents)
    private function getMyStudentIds($user)
    {
        $classStudents = ClassRoom::where('homeroom_teacher_id', $user->id)
            ->with('students:id')
            ->get()
            ->flatMap(fn($c) => $c->students->pluck('id'));

        $shadowStudents = ShadowGroup::where('pic_id', $user->id)
            ->orWhere('partner_id', $user->id)
            ->pluck('student_id');

        $oneOnOneStudents = OneOnOneGroup::where('teacher_id', $user->id)
            ->pluck('student_id');

        return $classStudents
            ->merge($shadowStudents)
            ->merge($oneOnOneStudents)
            ->unique()
            ->values();
    }

    // ── Bangun daftar anak + status laporan hari ini ─────────────────────────
    private function buildDaftarAnak($studentIds, $today)
    {
        $statusLabel = [
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin'  => 'Izin',
            'alpha' => 'Alpha',
        ];

        $reportsToday = DailyReport::whereIn('student_id', $studentIds)
            ->whereDate('date', $today)
            ->get(['id', 'student_id', 'attendance_status'])
            ->keyBy('student_id');

        return Student::whereIn('id', $studentIds)
            ->get(['id', 'name', 'photo'])
            ->values()
            ->map(function ($s, $index) use ($reportsToday, $statusLabel) {
                $report = $reportsToday->get($s->id);
                $sudah  = !is_null($report);

                return [
                    'id'     => $s->id,
                    'name'   => $s->name,
                    'avatar' => [
                        'initial' => strtoupper(substr($s->name, 0, 1)),
                        'color'   => $this->avatarColors[$index % count($this->avatarColors)],
                        'photo'   => $s->photo ?: null,
                    ],
                    'report_status'     => $sudah ? 'sudah' : 'belum',
                    'attendance_status' => $report?->attendance_status,
                    'attendance_label'  => $sudah ? ($statusLabel[$report->attendance_status] ?? 'Hadir') : 'Belum diisi',
                    'report_id'         => $report?->id ?? null,
                ];
            });
    }
}
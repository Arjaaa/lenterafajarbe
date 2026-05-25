<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today();
        $week  = Carbon::now()->startOfWeek();

        // ─── 1. STATS ────────────────────────────────────────────────────────

        $baseQuery = DailyReport::whereDate('date', $today);
        $baseQuery = $this->filterByRole($baseQuery, $user);

        $todayReports    = (clone $baseQuery)->count();
        $todayAttendance = (clone $baseQuery)->distinct('student_id')->count('student_id');
        $todayActivities = (clone $baseQuery)
            ->whereHas('detail', fn($q) => $q->whereNotNull('activity_notes')
                ->where('activity_notes', '!=', ''))
            ->count();
        $weeklyReports = DailyReport::whereBetween('date', [$week, Carbon::now()])
            ->when(true, fn($q) => $this->filterByRole($q, $user))
            ->count();

        // ─── 2. ANNOUNCEMENT ─────────────────────────────────────────────────

        $announcement = Announcement::where('is_active', true)
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $today))
            ->orderByDesc('created_at')
            ->first();

        // ─── 3. DOKUMENTASI TERBARU ──────────────────────────────────────────

        $docsReports = DailyReport::whereDate('date', $today)
            ->when(true, fn($q) => $this->filterByRole($q, $user))
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

        $latestReports = DailyReport::whereDate('date', $today)
            ->when(true, fn($q) => $this->filterByRole($q, $user))
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
                'latest_documentations' => $latestDocumentations,
                'latest_reports'        => $latestReports,
                'notifications_count'   => 0,
            ],
        ]);
    }

    private function filterByRole($query, $user)
    {
        return match ($user->role) {
            'shadow_teacher', 'shadow_pj'        => $query->where('shadow_teacher_id', $user->id),
            'therapist', 'therapist_homeroom'    => $query->where('therapist_id', $user->id),
            default                              => $query,
        };
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
}
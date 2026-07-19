<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\MonthlyReport;
use App\Models\Student;
use App\Models\StudentDocumentation;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class ParentReportController extends Controller
{
    /**
     * GET /api/parent/dashboard
     * Dashboard utama orang tua
     */
    public function dashboard(Request $request)
    {
        $parent   = $request->user();
        $children = Student::where('parent_id', $parent->id)
            ->with(['classes:id,name'])
            ->get();

        $dashboardData = $children->map(function ($student) {
            $nameParts = explode(' ', $student->name);
            $initials  = strtoupper(
                substr($nameParts[0], 0, 1) .
                (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '')
            );

            $class = $student->classes?->first();

            // Total laporan harian bulan ini
            $totalReports = DailyReport::where('student_id', $student->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count();

            // Mood positif % bulan ini (mood_arrival >= 4)
            $reportsThisMonth = DailyReport::with('detail')
                ->where('student_id', $student->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->get();

            $totalThisMonth   = $reportsThisMonth->count();
            $positiveCount    = $reportsThisMonth->filter(
                fn($r) => ($r->detail?->mood_arrival ?? 0) >= 4
            )->count();

            $moodPositivePct = $totalThisMonth > 0
                ? round(($positiveCount / $totalThisMonth) * 100, 1)
                : 0;

            // Total dokumentasi kegiatan
            $totalDokumentasi = StudentDocumentation::where('student_id', $student->id)
                ->count();

            // Total worksheet
            $totalWorksheet = Worksheet::where('student_id', $student->id)
                ->count();

            return [
                'student' => [
                    'id'       => $student->id,
                    'name'     => $student->name,
                    'photo'    => $student->photo,
                    'initials' => $initials,
                    'class'    => $class?->name,
                ],
                'stats' => [
                    'total_reports'    => $totalReports,
                    'mood_positive_pct'=> $moodPositivePct,
                    'total_dokumentasi'=> $totalDokumentasi,
                    'total_worksheet'  => $totalWorksheet,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'parent'  => [
                'id'   => $parent->id,
                'name' => $parent->name,
            ],
            'children' => $dashboardData,
        ]);
    }

    /**
     * GET /api/parent/children
     * Daftar anak milik orang tua yang login
     */
    public function children(Request $request)
    {
        $children = Student::where('parent_id', $request->user()->id)
            ->with(['classes:id,name', 'shadowGroup', 'oneOnOneGroup'])
            ->get();

        return response()->json($children);
    }

    /**
     * GET /api/parent/children/{studentId}/daily-reports
     * Laporan harian anak milik orang tua
     */
    public function dailyReports(Request $request, $studentId)
    {
        $student = Student::where('id', $studentId)
            ->where('parent_id', $request->user()->id)
            ->firstOrFail();

        $query = DailyReport::with([
            'detail',
            'shadowTeacher:id,name,role',
            'therapist:id,name,role',
        ])
        ->where('student_id', $studentId)
        ->latest('date');

        if ($request->has('month')) {
            $query->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$request->month]);
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        $reports = $query->get()->map(fn($r) => $this->enrichAbsentInfo($r));

        return response()->json([
            'student' => $student->only(['id', 'name', 'photo']),
            'reports' => $reports,
        ]);
    }

    /**
     * GET /api/parent/children/{studentId}/daily-reports/{reportId}
     * Detail laporan harian 1 anak
     */
    public function showDailyReport(Request $request, $studentId, $reportId)
    {
        Student::where('id', $studentId)
            ->where('parent_id', $request->user()->id)
            ->firstOrFail();

        $report = DailyReport::with([
            'detail',
            'student:id,name,photo',
            'shadowTeacher:id,name,role',
            'therapist:id,name,role',
        ])
        ->where('student_id', $studentId)
        ->findOrFail($reportId);

        return response()->json($this->enrichAbsentInfo($report));
    }

    /**
     * Tambahkan info tampilan siap-pakai ketika laporan berstatus
     * tidak hadir (sakit/izin/alpha) — supaya FE tidak menampilkan
     * null / "-" polos untuk laporan yang memang tidak punya detail.
     */
    private function enrichAbsentInfo(DailyReport $report): array
    {
        $data     = $report->toArray();
        $isAbsent = $report->attendance_status !== 'hadir';

        $data['attendance_status_label'] = ucfirst($report->attendance_status);

        if ($isAbsent) {
            $absentInfo = [
                'sakit' => ['title' => 'Sakit', 'emoji' => '🤒', 'status' => 'Perlu Perhatian', 'color' => '#F5A623'],
                'izin'  => ['title' => 'Izin',  'emoji' => '📄', 'status' => 'Izin',            'color' => '#4A90E2'],
                'alpha' => ['title' => 'Alpha', 'emoji' => '❌', 'status' => 'Perlu Perhatian',  'color' => '#FF4D4F'],
            ][$report->attendance_status] ?? [
                'title' => 'Tidak Hadir', 'emoji' => '❓', 'status' => 'Perlu Perhatian', 'color' => '#8C8C8C',
            ];

            $data['display'] = [
                'title'       => $absentInfo['title'],
                'emoji'       => $absentInfo['emoji'],
                'status_tag'  => $absentInfo['status'],
                'status_color'=> $absentInfo['color'],
                'summary'     => "Tidak masuk karena {$absentInfo['title']}",
                'condition_summary' => '-',
            ];
        } else {
            $activityNotes = $report->detail?->activity_notes;
            $data['display'] = [
                'title'       => $activityNotes ?: 'Laporan Harian',
                'emoji'       => $this->moodEmoji($report->detail?->mood_arrival),
                'status_tag'  => $this->moodStatus($report->detail?->mood_arrival),
                'status_color'=> null,
                'summary'     => $activityNotes,
                'condition_summary' => trim(
                    ($report->detail?->physical_condition_arrival_label ?? '') .
                    ($report->detail?->independence_label ? ' · ' . $report->detail->independence_label : '')
                , ' ·'),
            ];
        }

        return $data;
    }
    /**
 * GET /api/parent/children/{studentId}/home
 * Dashboard home orang tua per anak
 */
public function home(Request $request, $studentId)
{
    $student = Student::where('id', $studentId)
        ->where('parent_id', $request->user()->id)
        ->firstOrFail();

    // 3 laporan harian terakhir
    $latestReports = DailyReport::with(['detail', 'shadowTeacher:id,name', 'therapist:id,name'])
        ->where('student_id', $studentId)
        ->latest('date')
        ->take(3)
        ->get()
        ->map(fn($r) => [
            'id'             => $r->id,
            'date'           => $r->date,
            'activity_notes' => $r->detail?->activity_notes,
            'mood_arrival'   => $r->detail?->mood_arrival,
            'mood_end'       => $r->detail?->mood_end,
            'physical_condition_arrival' => $r->detail?->physical_condition_arrival,
            'teacher'        => $r->therapist?->name ?? $r->shadowTeacher?->name,
        ]);

    // Foto kegiatan dari 3 laporan terakhir (photo_activity saja)
    $activityPhotos = DailyReport::with('detail')
    ->where('student_id', $studentId)
    ->latest('date')
    ->take(18)
    ->get()
    ->flatMap(fn($r) => $r->detail?->photo_activity ?? [])
    ->filter()
    ->take(4)
    ->values();

    return response()->json([
        'success' => true,
        'data'    => [
            'student' => [
                'id'    => $student->id,
                'name'  => $student->name,
                'photo' => $student->photo,
            ],
            'latest_reports'   => $latestReports,
            'activity_photos'  => $activityPhotos,
        ],
    ]);
}
/**
 * GET /api/parent/children/{studentId}/report-history
 * History catatan harian dengan filter
 * 
 * Query params:
 * - filter: all | today | week | month
 * - date: YYYY-MM-DD (pilih tanggal spesifik)
 */
public function reportHistory(Request $request, $studentId)
{
    $student = Student::where('id', $studentId)
        ->where('parent_id', $request->user()->id)
        ->with('classes:id,name')
        ->firstOrFail();

    $query = DailyReport::with(['detail', 'classification', 'shadowTeacher:id,name', 'therapist:id,name'])
        ->where('student_id', $studentId)
        ->latest('date');

    // Filter
    $filter = $request->input('filter', 'all');

    if ($request->has('date')) {
        $query->whereDate('date', $request->date);
    } elseif ($filter === 'today') {
        $query->whereDate('date', today());
    } elseif ($filter === 'week') {
        $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($filter === 'month') {
        $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }

    $reports   = $query->get();
    $latestId  = $reports->first()?->id;

    $formatted = $reports->map(function ($r) use ($latestId) {
        $isAbsent = $r->attendance_status !== 'hadir';

        // ─── Laporan tidak hadir (sakit/izin/alpha) — tidak punya detail ────
        if ($isAbsent) {
            $absentInfo = [
                'sakit' => ['title' => 'Sakit', 'emoji' => '🤒', 'status' => 'Perlu Perhatian'],
                'izin'  => ['title' => 'Izin',  'emoji' => '📄', 'status' => 'Izin'],
                'alpha' => ['title' => 'Alpha', 'emoji' => '❌', 'status' => 'Perlu Perhatian'],
            ][$r->attendance_status] ?? ['title' => 'Tidak Hadir', 'emoji' => '❓', 'status' => 'Perlu Perhatian'];

            return [
                'id'                         => $r->id,
                'date'                       => $r->date,
                'is_new'                     => $r->id === $latestId,
                'attendance_status'          => $r->attendance_status,
                'attendance_status_label'    => ucfirst($r->attendance_status),
                'mood_arrival'               => null,
                'mood_label'                 => $absentInfo['title'],
                'mood_emoji'                 => $absentInfo['emoji'],
                'mood_status'                => $absentInfo['status'],
                'activity_notes'             => $absentInfo['title'],
                'physical_condition_arrival' => null,
                'independence'               => null,
                'behavior'                   => null,
                'has_homework'               => null,
                'has_challenge'              => null,
                'overall_score'              => null,
                'teacher'                    => $r->therapist?->name ?? $r->shadowTeacher?->name,
            ];
        }

        return [
            'id'          => $r->id,
            'date'        => $r->date,
            'is_new'      => $r->id === $latestId,
            'attendance_status'        => $r->attendance_status,
            'attendance_status_label' => 'Hadir',
            'mood_arrival'=> $r->detail?->mood_arrival,
            'mood_label'  => $this->moodLabel($r->detail?->mood_arrival),
            'mood_emoji'  => $this->moodEmoji($r->detail?->mood_arrival),
            'mood_status' => $this->moodStatus($r->detail?->mood_arrival),
            'activity_notes'             => $r->detail?->activity_notes,
            'physical_condition_arrival' => $r->detail?->physical_condition_arrival,
            'independence'               => $r->detail?->independence,
            'behavior'                   => $r->detail?->behavior,
            'has_homework'               => $r->detail?->has_homework,
            'has_challenge'              => $r->classification?->has_challenge,
            'overall_score'              => $r->classification?->overall_score,
            'teacher'                    => $r->therapist?->name ?? $r->shadowTeacher?->name,
        ];
    });

    // Group by month-year
    $grouped = $formatted->groupBy(fn($r) => \Carbon\Carbon::parse($r['date'])->format('F Y'))
        ->map(fn($items, $month) => [
            'month'   => strtoupper($month),
            'reports' => $items->values(),
        ])
        ->values();

    return response()->json([
        'success'       => true,
        'student'       => [
            'id'    => $student->id,
            'name'  => $student->name,
            'class' => $student->classes?->first()?->name,
        ],
        'filter'        => $filter,
        'total_reports' => $reports->count(),
        'data'          => $grouped,
    ]);
}
/**
 * GET /api/parent/children/{studentId}/documentation
 * Dokumentasi foto & video dari daily report
 *
 * Query params:
 * - period: all | 1_month | 3_months | 6_months
 * - type: all | photo | video
 * - date: YYYY-MM-DD
 */
public function documentation(Request $request, $studentId)
{
    $student = Student::where('id', $studentId)
        ->where('parent_id', $request->user()->id)
        ->with('classes:id,name')
        ->firstOrFail();

    $query = DailyReport::with('detail')
        ->where('student_id', $studentId)
        ->latest('date');

    // Filter periode
    $period = $request->input('period', 'all');
    if ($request->has('date')) {
        $query->whereDate('date', $request->date);
    } elseif ($period === '1_month') {
        $query->where('date', '>=', now()->subMonth());
    } elseif ($period === '3_months') {
        $query->where('date', '>=', now()->subMonths(3));
    } elseif ($period === '6_months') {
        $query->where('date', '>=', now()->subMonths(6));
    }

    // Kumpulkan semua media dulu untuk stats
    $allReports = $query->get();
    $allMedia   = $this->extractMedia($allReports, $request->input('type', 'all'));

    $totalPhoto   = collect($allMedia)->where('type', 'photo')->count();
    $totalVideo   = collect($allMedia)->where('type', 'video')->count();
    $hariTercatat = $allReports->filter(fn($r) =>
        !empty($r->detail?->photo_physical) ||
        !empty($r->detail?->photo_activity) ||
        !empty($r->detail?->photo_other)
    )->count();

    $perPage = 5;
    $cursor  = $request->input('cursor', 0);
    $paged   = collect($allMedia)->slice($cursor, $perPage)->values();
    $hasMore = collect($allMedia)->count() > ($cursor + $perPage);

    return response()->json([
        'success' => true,
        'student' => [
            'id'       => $student->id,
            'name'     => $student->name,
            'photo'    => $student->photo,
            'initials' => strtoupper(substr($student->name, 0, 1) . (strpos($student->name, ' ') !== false ? substr($student->name, strpos($student->name, ' ') + 1, 1) : '')),
            'class'    => $student->classes?->first()?->name,
        ],
        'stats' => [
            'total_photo'   => $totalPhoto,
            'total_video'   => $totalVideo,
            'hari_tercatat' => $hariTercatat,
        ],
        'filter' => [
            'period' => $period,
            'type'   => $request->input('type', 'all'),
        ],
        'media' => $paged,
        'meta'  => [
            'next_cursor' => $hasMore ? $cursor + $perPage : null,
            'has_more'    => $hasMore,
            'total'       => collect($allMedia)->count(),
        ],
    ]);
}

private function extractMedia($reports, string $type = 'all'): array
{
    $media = [];
    foreach ($reports as $r) {
        $d = $r->detail;
        if (!$d) continue;

        $allUrls = array_merge(
            $d->photo_physical ?? [],
            $d->photo_activity ?? [],
            $d->photo_other    ?? [],
        );

        foreach ($allUrls as $url) {
            if (empty($url)) continue;
            $isVideo    = (bool) preg_match('/\.(mp4|mov|avi|mkv|webm)/i', $url);
            $mediaType  = $isVideo ? 'video' : 'photo';

            if ($type !== 'all' && $mediaType !== $type) continue;

            $media[] = [
                'url'  => $url,
                'type' => $mediaType,
                'date' => $r->date,
            ];
        }
    }
    return $media;
}

private function moodLabel(?int $mood): string
{
    return match($mood) {
        1 => 'Sangat Sedih', 2 => 'Sedih', 3 => 'Biasa',
        4 => 'Senang', 5 => 'Sangat Senang', default => '-',
    };
}

private function moodEmoji(?int $mood): string
{
    return match($mood) {
        1 => '😢', 2 => '😔', 3 => '😐',
        4 => '😊', 5 => '😄', default => '😐',
    };
}

private function moodStatus(?int $mood): string
{
    return match(true) {
        $mood >= 4  => 'Mood Positif',
        $mood === 3 => 'Mood Netral',
        $mood < 3   => 'Perlu Perhatian',
        default     => '-',
    };
}
}
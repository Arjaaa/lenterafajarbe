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

        $reports = $query->get();

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

        return response()->json($report);
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
        ->take(4)
        ->get()
        ->flatMap(fn($r) => $r->detail?->photo_activity ?? [])
        ->filter()
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
}
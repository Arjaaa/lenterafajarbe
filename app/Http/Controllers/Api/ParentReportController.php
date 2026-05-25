<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Student;
use Illuminate\Http\Request;

class ParentReportController extends Controller
{
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
     * Query params: ?month=2026-05 atau ?date=2026-05-07
     */
    public function dailyReports(Request $request, $studentId)
    {
        // Pastikan anak ini milik orang tua yang login
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
        // Pastikan anak ini milik orang tua yang login
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
}
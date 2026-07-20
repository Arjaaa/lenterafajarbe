<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentProfileController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ShadowGroupController;
use App\Http\Controllers\Api\OneOnOneGroupController;
use App\Http\Controllers\Api\DailyReportController;
use App\Http\Controllers\Api\ParentReportController;
use App\Http\Controllers\Api\MonthlyReportController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ClassDashboardController;
use App\Http\Controllers\Api\SchoolHolidayController;
use App\Http\Controllers\Api\TeacherReportController;
use App\Http\Controllers\Api\StudentDocumentationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorksheetController;
use App\Http\Controllers\Api\CoordinatorDashboardController;

// ─── PUBLIC ROUTES ────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
// /register dipindah ke bawah, protected coordinator_main only

// ─── PROTECTED ROUTES ─────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', fn(Request $request) => $request->user());

    // ── DASHBOARD + KELAS (teacher & coordinator) ─────────────────────────────
    Route::middleware('role:teacher,coordinator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/class-dashboard', [ClassDashboardController::class, 'index']);
        Route::get('/student-list/{classId}', [ClassDashboardController::class, 'show']);
        Route::get('/my-students', [DailyReportController::class, 'myStudents']);

        Route::get('/students/{studentId}/profile', [StudentProfileController::class, 'show']);
        Route::put('/students/{studentId}/profile', [StudentProfileController::class, 'update']);

        Route::get('/students/{studentId}/documentations', [StudentDocumentationController::class, 'index']);
        Route::get('/students/{studentId}/documentations/summary', [StudentDocumentationController::class, 'summary']);
        Route::get('/students/{studentId}/documentations/{id}', [StudentDocumentationController::class, 'show']);
    });

    Route::middleware('role:therapist_homeroom,coordinator')->group(function () {
        Route::post('/students/{studentId}/documentations/upload', [StudentDocumentationController::class, 'store']);
        Route::put('/students/{studentId}/documentations/{id}', [StudentDocumentationController::class, 'update']);
        Route::delete('/students/{studentId}/documentations/{id}', [StudentDocumentationController::class, 'destroy']);
    });

    // ── COORDINATOR MAIN ONLY ─────────────────────────────────────────────────
    Route::middleware('role:coordinator_main')->group(function () {

        // Register staff (teacher, therapist, shadow, coordinator lain)
        Route::post('/register', [RegisterController::class, 'register']);

        // Users
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}/activate', [UserController::class, 'activate']);
        Route::put('/users/{id}/deactivate', [UserController::class, 'deactivate']);
        Route::put('/users/{id}/role', [UserController::class, 'assignRole']);

        // Murid
        Route::get('/students/special-needs-options', [StudentController::class, 'specialNeedsOptions']);
        Route::get('/students/{id}/dashboard', [StudentController::class, 'dashboard']);
        Route::apiResource('students', StudentController::class);

        // Kelas
        Route::apiResource('classes', ClassController::class);  
        Route::post('classes/{id}/students', [ClassController::class, 'addStudent']);
        Route::put('classes/{id}/students/{studentId}', [ClassController::class, 'updateStudent']);
        Route::delete('classes/{id}/students/{studentId}', [ClassController::class, 'removeStudent']);
        Route::post('classes/{id}/attach-students', [ClassController::class, 'attachStudents']);

        // Assign teacher/therapist/shadow ke kelas → sekaligus aktivasi
        Route::post('classes/{id}/staff', [ClassController::class, 'addStaff']);
        Route::delete('classes/{id}/staff/{userId}', [ClassController::class, 'removeStaff']);

        // Group Shadow Teacher
        Route::apiResource('shadow-groups', ShadowGroupController::class);

        // Group One on One
        Route::apiResource('one-on-one-groups', OneOnOneGroupController::class);

        // ── WEBSITE COORDINATOR ─────────────────────────────────────────────────────
        Route::get('/coordinator/dashboard', [CoordinatorDashboardController::class, 'index']);
        Route::get('/coordinator/daily-reports', [CoordinatorDashboardController::class, 'dailyReports']);
        Route::get('/coordinator/worksheets', [CoordinatorDashboardController::class, 'worksheets']);
        Route::get('/coordinator/teacher-reports', [CoordinatorDashboardController::class, 'teacherReports']);
        Route::get('/coordinator/teachers', [CoordinatorDashboardController::class, 'allTeachers']);
        Route::get('/coordinator/students/{studentId}/documentation', [CoordinatorDashboardController::class, 'studentDocumentation']);
        Route::get('/coordinator/monthly-reports', [CoordinatorDashboardController::class, 'allMonthlyReports']);
    });

    // ── ANNOUNCEMENT ───────────────────────────────────────────────────────────
    Route::middleware('role:coordinator_main')->group(function () {
        Route::get('/announcements', [AnnouncementController::class, 'index']);
        Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
        Route::post('/announcements', [AnnouncementController::class, 'store']);
        Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
        Route::patch('/announcements/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
    });

    Route::middleware('role:teacher,coordinator')->group(function () {
        Route::get('/teacher/announcements', [AnnouncementController::class, 'index']);
        Route::get('/teacher/announcements/{id}', [AnnouncementController::class, 'show']);
    });

    Route::middleware('role:parent')->group(function () {
        Route::get('/parent/announcements', [AnnouncementController::class, 'index']);
        Route::get('/parent/announcements/{id}', [AnnouncementController::class, 'show']);
    });

    // ── LAPORAN HARIAN ────────────────────────────────────────────────────────
    Route::middleware('role:teacher,coordinator')->group(function () {
        Route::get('/daily-reports/form-options', [DailyReportController::class, 'formOptions']);
        Route::get('/daily-reports', [DailyReportController::class, 'index']);
        Route::get('/daily-reports/{id}', [DailyReportController::class, 'show']);
        Route::get('/students/{id}/dashboard', [StudentController::class, 'dashboard']);
    });

    Route::middleware('role:teacher')->group(function () {
        Route::post('/daily-reports', [DailyReportController::class, 'store']);
        Route::post('/daily-reports/{id}', [DailyReportController::class, 'update']);
        Route::delete('/daily-reports/{id}', [DailyReportController::class, 'destroy']);
    });

    // ── ORANG TUA ─────────────────────────────────────────────────────────────
    Route::middleware('role:parent')->prefix('parent')->group(function () {
        Route::get('/children', [ParentReportController::class, 'children']);
        Route::get('/children/{studentId}/daily-reports', [ParentReportController::class, 'dailyReports']);
        Route::get('/children/{studentId}/daily-reports/{reportId}', [ParentReportController::class, 'showDailyReport']);
        Route::get('/children/{studentId}/monthly-reports', [MonthlyReportController::class, 'parentView']);
        Route::get('/dashboard', [ParentReportController::class, 'dashboard']);
        Route::get('/children/{studentId}/home', [ParentReportController::class, 'home']);
        Route::get('/children/{studentId}/report-history', [ParentReportController::class, 'reportHistory']);
        Route::get('/children/{studentId}/documentation', [ParentReportController::class, 'documentation']);
    });

    // Monthly Report
    Route::middleware('role:teacher,coordinator')->group(function () {
        Route::get('/monthly-reports', [MonthlyReportController::class, 'index']);
        Route::get('/monthly-reports/student/{studentId}', [MonthlyReportController::class, 'byStudent']);
        Route::get('/monthly-reports/{id}', [MonthlyReportController::class, 'show']);
        Route::post('/monthly-reports/generate', [MonthlyReportController::class, 'generate']);
        Route::put('/monthly-reports/{id}/coordinator-note', [MonthlyReportController::class, 'coordinatorNote']);
    });

    // ── SCHOOL HOLIDAYS ───────────────────────────────────────────────────────
    Route::middleware('role:teacher,coordinator')->group(function () {
        Route::get('/school-holidays', [SchoolHolidayController::class, 'index']);
    });

    Route::middleware('role:coordinator_main')->group(function () {
        Route::post('/school-holidays', [SchoolHolidayController::class, 'store']);
        Route::put('/school-holidays/{id}', [SchoolHolidayController::class, 'update']);
        Route::delete('/school-holidays/{id}', [SchoolHolidayController::class, 'destroy']);
    });

    // ── WORKSHEET ─────────────────────────────────────────────────────────────
    Route::middleware('role:teacher,coordinator')->group(function () {
        Route::get('/worksheets/summary', [WorksheetController::class, 'summary']);
        Route::get('/worksheets', [WorksheetController::class, 'index']);
        Route::get('/worksheets/{id}', [WorksheetController::class, 'show']);
        Route::post('/worksheets/upload', [WorksheetController::class, 'store']);
        Route::put('/worksheets/{id}', [WorksheetController::class, 'update']);
        Route::delete('/worksheets/{id}', [WorksheetController::class, 'destroy']);
    });

    // ── TEACHER MONTHLY REPORTS ───────────────────────────────────────────────
    Route::middleware('role:teacher,coordinator')->prefix('teacher-reports')->group(function () {
        Route::get('/monthly', [TeacherReportController::class, 'monthlyIndex']);
        Route::get('/monthly/my-report', [TeacherReportController::class, 'monthlyMyReport']);
        Route::get('/monthly/teacher/{teacherId}', [TeacherReportController::class, 'monthlyByTeacher']);
        Route::get('/monthly/{id}', [TeacherReportController::class, 'monthlyShow']);

        Route::get('/annual', [TeacherReportController::class, 'annualIndex']);
        Route::get('/annual/my-report', [TeacherReportController::class, 'annualMyReport']);
        Route::get('/annual/teacher/{teacherId}', [TeacherReportController::class, 'annualByTeacher']);
        Route::get('/annual/{id}', [TeacherReportController::class, 'annualShow']);
    });

    Route::middleware('role:coordinator_main')->prefix('teacher-reports')->group(function () {
        Route::post('/monthly/generate', [TeacherReportController::class, 'monthlyGenerate']);
        Route::post('/monthly/stop-teacher', [TeacherReportController::class, 'stopTeacherPeriod']);
        Route::post('/monthly/start-teacher', [TeacherReportController::class, 'startTeacherPeriod']);
        Route::post('/annual/generate', [TeacherReportController::class, 'annualGenerate']);
        Route::put('/monthly/{id}/recommendation', [TeacherReportController::class, 'monthlyRecommendation']);
        Route::put('/annual/{id}/recommendation', [TeacherReportController::class, 'annualRecommendation']);
    });

});
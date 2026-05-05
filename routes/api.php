<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\StudentController;

// ─── PUBLIC ROUTES ─────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// ─── PROTECTED ROUTES ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // TEACHER ONLY
    Route::middleware('role:teacher')->group(function () {
        Route::post('/students', [StudentController::class, 'store']);
        Route::put('/students/{id}', [StudentController::class, 'update']);
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    });

    // TEACHER + PARENT
    Route::middleware('role:teacher,parent')->group(function () {
        Route::get('/students', [StudentController::class, 'index']);
        Route::get('/students/{id}', [StudentController::class, 'show']);
    });

    // SEMUA ROLE
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

});
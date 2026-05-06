<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ShadowGroupController;
use App\Http\Controllers\Api\OneOnOneGroupController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::middleware('role:coordinator_main')->group(function () {

        Route::apiResource('classes', ClassController::class);
        Route::post('classes/{id}/students', [ClassController::class, 'addStudent']);
        Route::put('classes/{id}/students/{studentId}', [ClassController::class, 'updateStudent']);
        Route::delete('classes/{id}/students/{studentId}', [ClassController::class, 'removeStudent']);

        Route::apiResource('shadow-groups', ShadowGroupController::class);

        Route::apiResource('one-on-one-groups', OneOnOneGroupController::class);
    });

    Route::middleware('role:teacher')->group(function () {
        Route::post('/students', [StudentController::class, 'store']);
        Route::put('/students/{id}', [StudentController::class, 'update']);
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    });

    Route::middleware('role:teacher,parent')->group(function () {
        Route::get('/students', [StudentController::class, 'index']);
        Route::get('/students/{id}', [StudentController::class, 'show']);
    });

});
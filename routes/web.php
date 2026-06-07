<?php

use Illuminate\Support\Facades\Route;

// Import semua controller yang baru kita buat
use App\Http\Controllers\Koor\DashboardController;
use App\Http\Controllers\Koor\AnakController;
use App\Http\Controllers\Koor\OrangTuaController;
use App\Http\Controllers\Koor\GuruController;
use App\Http\Controllers\Koor\KelasController;
use App\Http\Controllers\Koor\ShadowController;
use App\Http\Controllers\Koor\OneOnOneController;

Route::get('/', function () {
    return view('welcome');
});

// Rute untuk nampilin halaman UI Login
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');

// Rute untuk memproses data email & password saat tombol "Masuk" diklik
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.process');

// Rute untuk Logout
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// KELOMPOK RUTE KOORDINATOR
// FIX: Tambahkan name('koor.') di group ini agar semua route di dalamnya berawalan koor.
Route::middleware(['auth'])->prefix('koor')->name('koor.')->group(function () {

    Route::get('/dashboard-koor', [DashboardController::class, 'index'])->name('dashboard');

    // DATA ANAK
    Route::controller(AnakController::class)->prefix('data-anak')->group(function () {
        Route::get('/', 'dataAnak')->name('dataAnak');
        Route::post('/simpan', 'storeAnak')->name('storeAnak');
        Route::put('/update/{id}', 'updateAnak')->name('updateAnak');
        Route::delete('/hapus/{id}', 'destroyAnak')->name('destroyAnak');
    });

    // DATA ORANG TUA
    Route::controller(OrangTuaController::class)->prefix('data-orang-tua')->group(function () {
        Route::get('/', 'dataOrangTua')->name('dataOrangTua');
        Route::post('/simpan', 'storeOrangTua')->name('storeOrangTua');
        Route::put('/update/{id}', 'updateOrangTua')->name('updateOrangTua');
        Route::delete('/hapus/{id}', 'destroyOrangTua')->name('destroyOrangTua');
    });

    // DATA GURU
    Route::controller(GuruController::class)->prefix('data-guru')->group(function () {
        Route::get('/', 'dataGuru')->name('dataGuru');
        Route::post('/simpan', 'storeGuru')->name('storeGuru');
        Route::put('/update/{id}', 'updateGuru')->name('updateGuru');
        Route::delete('/hapus/{id}', 'destroyGuru')->name('destroyGuru');
    });

    // DATA KELAS UMUM
    Route::controller(KelasController::class)->prefix('data-kelas')->group(function () {
        Route::get('/', 'dataKelas')->name('dataKelas');
        Route::post('/simpan', 'storeKelas')->name('storeKelas');
        Route::put('/update/{id}', 'updateKelas')->name('updateKelas');
        Route::delete('/hapus/{id}', 'destroyKelas')->name('destroyKelas');

        Route::get('/detail/{id}', 'detailKelas')->name('detailKelas');
        Route::post('/detail/{id}/tambah-murid', 'tambahMuridKeKelas')->name('tambahMuridKeKelas');
        Route::delete('/detail/{classId}/keluarkan/{studentId}', 'keluarkanMuridDariKelas')->name('keluarkanMurid');
    });

    // DATA GROUP SHADOW
    Route::controller(ShadowController::class)->prefix('data-shadow')->group(function () {
        Route::get('/', 'dataShadowGroup')->name('dataShadowGroup');
        Route::post('/simpan', 'storeShadowGroup')->name('storeShadowGroup');
        Route::put('/update/{id}', 'updateShadowGroup')->name('updateShadowGroup');
        Route::delete('/hapus/{id}', 'destroyShadowGroup')->name('destroyShadowGroup');

        Route::get('/detail/{id}', 'show')->name('shadow.show');
    });

    // DATA KELAS 1 ON 1
    Route::controller(OneOnOneController::class)->prefix('data-1on1')->group(function () {
        Route::get('/', 'data1on1')->name('data1on1');
        Route::post('/simpan', 'store1on1')->name('store1on1');
        Route::put('/update/{id}', 'update1on1')->name('update1on1');
        Route::delete('/hapus/{id}', 'destroy1on1')->name('destroy1on1');

        Route::get('/detail/{id}', 'show')->name('oneonone.show');
    });
});
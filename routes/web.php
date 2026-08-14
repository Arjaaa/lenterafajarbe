<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Koor\DashboardController;
use App\Http\Controllers\Koor\AnakController;
use App\Http\Controllers\Koor\OrangTuaController;
use App\Http\Controllers\Koor\GuruController;
use App\Http\Controllers\Koor\KelasController;
use App\Http\Controllers\Koor\ShadowController;
use App\Http\Controllers\Koor\OneOnOneController;
use App\Http\Controllers\Koor\WorksheetController;
use App\Http\Controllers\Koor\DailyReportController;
use App\Http\Controllers\Koor\LaporanGuruController;
use App\Http\Controllers\Koor\RaportSiswaController; // <-- TAMBAHIN INI DI ATAS

Route::get('/', function () {
    return view('welcome');
});

// Rute untuk yang belum login (guest)
Route::middleware(['api.guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// Rute untuk Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// SEMUA RUTE DI DALAM SINI OTOMATIS PUNYA AWALAN NAMA 'koor.'
// =========================================================================
Route::middleware(['api.auth'])->prefix('koor')->name('koor.')->group(function () {

    Route::get('/dashboard-koor', [DashboardController::class, 'index'])->name('dashboard');

    // DATA ANAK
    Route::controller(AnakController::class)->prefix('data-anak')->group(function () {
        Route::get('/', 'dataAnak')->name('dataAnak');
        Route::post('/simpan', 'storeAnak')->name('storeAnak');
        Route::put('/update/{id}', 'updateAnak')->name('updateAnak');
        Route::delete('/hapus/{id}', 'destroyAnak')->name('destroyAnak');

        Route::get('/detail/{id}', 'showAnak')->name('detailAnak');
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

        Route::get('/detail/{id}', 'detailShadowGroup')->name('detailShadowGroup');
    });

    // DATA KELAS 1 ON 1 (SUDAH DI-FIX, HAPUS AWALAN KOOR.)
    Route::controller(OneOnOneController::class)->prefix('data-1on1')->group(function () {
        Route::get('/', 'index')->name('data1on1');
        Route::post('/simpan', 'store')->name('store1on1');
        Route::put('/update/{id}', 'update')->name('update1on1');
        Route::delete('/hapus/{id}', 'destroy')->name('destroy1on1');

        Route::get('/detail/{id}', 'detail1on1')->name('detail1on1');
    });

    // DATA TEACHER WORKSHEET
    Route::controller(WorksheetController::class)->prefix('teacher-worksheet')->group(function () {
        Route::get('/', 'index')->name('worksheet.index');
        Route::post('/simpan', 'store')->name('worksheet.store');
    });

    // DATA DAILY REPORT (PERKEMBANGAN ANAK)
    Route::controller(DailyReportController::class)->prefix('perkembangan-anak')->group(function () {
        Route::get('/', 'index')->name('dailyReport.index');
        Route::get('/detail/{id}', 'detail')->name('dailyReport.detail');
    });
    // DATA RAPORT SISWA
    Route::controller(RaportSiswaController::class)->prefix('raport-siswa')->group(function () {
        Route::get('/', 'index')->name('raportSiswa');          // Otomatis menjadi: koor.raportSiswa
        Route::get('/{id}', 'detailRaport')->name('detailRaport'); // Otomatis menjadi: koor.detailRaport
    });
    Route::controller(LaporanGuruController::class)->prefix('rapor-guru')->group(function () {
        Route::get('/', 'index')->name('raporGuru');
        Route::get('/detail/{id}', 'detailRapor')->name('raporGuruDetail');
        Route::post('/feedback/{reportId}', 'storeFeedback')->name('raporGuru.feedback'); // <-- BARU
    });
});
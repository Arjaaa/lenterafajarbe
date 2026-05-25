<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan tiap hari jam 23:30
        // Cek apakah hari ini hari terakhir bulan — kalau ya, generate monthly report
        $schedule->call(function () {
            // Cek hari ini adalah hari terakhir bulan ini
            if (now()->day === now()->daysInMonth) {
                \Illuminate\Support\Facades\Artisan::call('reports:generate-monthly', [
                    '--month' => now()->month,
                    '--year'  => now()->year,
                ]);
            }
        })->dailyAt('23:30')->name('generate-monthly-reports');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
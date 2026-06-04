<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
{
    // Generate monthly report murid — tiap akhir bulan jam 23:30
    $schedule->call(function () {
        if (now()->day === now()->daysInMonth) {
            \Illuminate\Support\Facades\Artisan::call('reports:generate-monthly', [
                '--month' => now()->month,
                '--year'  => now()->year,
            ]);
        }
    })->dailyAt('23:30')->name('generate-monthly-reports');

    // Generate monthly report guru — tiap akhir bulan jam 23:45
    $schedule->call(function () {
        if (now()->day === now()->daysInMonth) {
            \Illuminate\Support\Facades\Artisan::call('reports:generate-teacher', [
                '--month' => now()->month,
                '--year'  => now()->year,
            ]);
        }
        // Generate annual report guru — tiap akhir Juni (akhir tahun ajaran)
        if (now()->month === 6 && now()->day === now()->daysInMonth) {
            \Illuminate\Support\Facades\Artisan::call('reports:generate-teacher', [
                '--type'          => 'annual',
                '--academic-year' => (now()->year - 1) . '/' . now()->year,
            ]);
        }
    })->dailyAt('23:45')->name('generate-teacher-reports');
}

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
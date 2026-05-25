<?php

namespace App\Console\Commands;

use App\Services\MonthlyReportService;
use Illuminate\Console\Command;

class GenerateMonthlyReports extends Command
{
    protected $signature   = 'reports:generate-monthly {--month= : Bulan (1-12)} {--year= : Tahun}';
    protected $description = 'Generate laporan bulanan untuk semua murid';

    public function handle(MonthlyReportService $service): void
    {
        // Default: bulan kemarin (karena dijalankan di akhir bulan / awal bulan baru)
        $month = (int) ($this->option('month') ?? now()->subMonth()->month);
        $year  = (int) ($this->option('year')  ?? now()->subMonth()->year);

        $this->info("Generating monthly reports untuk {$month}/{$year}...");

        $results = $service->generateForAllStudents($month, $year);

        $success = collect($results)->where('status', 'success')->count();
        $failed  = collect($results)->where('status', 'failed')->count();

        $this->info("Selesai: {$success} berhasil, {$failed} gagal.");

        if ($failed > 0) {
            $this->warn("Yang gagal:");
            foreach (collect($results)->where('status', 'failed') as $r) {
                $this->warn("  - Student ID {$r['student_id']}: {$r['error']}");
            }
        }
    }
}
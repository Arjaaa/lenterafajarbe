<?php

namespace App\Console\Commands;

use App\Services\TeacherReportService;
use Illuminate\Console\Command;

class GenerateTeacherReports extends Command
{
    protected $signature = 'reports:generate-teacher
                            {--month= : Bulan (default: bulan ini)}
                            {--year=  : Tahun (default: tahun ini)}
                            {--type=monthly : monthly atau annual}
                            {--academic-year= : Tahun ajaran untuk annual (misal: 2025/2026)}';

    protected $description = 'Generate laporan performa guru';

    public function handle(TeacherReportService $service): void
    {
        $type = $this->option('type');

        if ($type === 'annual') {
            $academicYear = $this->option('academic-year')
                ?? $service->getAcademicYear(now()->month, now()->year);

            $this->info("Generate annual report tahun ajaran {$academicYear}...");
            $results = $service->generateAnnualForAll($academicYear);
        } else {
            $month = (int) ($this->option('month') ?: now()->month);
            $year  = (int) ($this->option('year')  ?: now()->year);

            $this->info("Generate monthly teacher report {$month}/{$year}...");
            $results = $service->generateMonthlyForAll($month, $year);
        }

        $success = collect($results)->where('status', 'success')->count();
        $failed  = collect($results)->where('status', 'failed')->count();

        $this->info("Selesai! Sukses: {$success}, Gagal: {$failed}");
    }
}
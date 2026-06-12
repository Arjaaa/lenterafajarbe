<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_monthly_reports', function (Blueprint $table) {

            // ── Laporan Harian ────────────────────────────────────────────
            $table->decimal('report_completeness_pct', 5, 2)->nullable()->after('completeness_score');   // % laporan terisi lengkap
            $table->decimal('timeliness_score', 5, 2)->nullable()->after('report_completeness_pct');     // % laporan masuk sebelum jam 16
            $table->decimal('weekly_consistency', 5, 2)->nullable()->after('timeliness_score');          // minggu dengan laporan / total minggu
            $table->unsignedInteger('longest_streak')->default(0)->after('weekly_consistency');          // streak terpanjang (hari)
            $table->decimal('avg_fill_time_minutes', 8, 2)->nullable()->after('longest_streak');         // rata-rata menit pengisian

            // ── Kondisi & Mood Anak ───────────────────────────────────────
            $table->decimal('physical_health_pct', 5, 2)->nullable()->after('avg_fill_time_minutes');    // % kondisi fisik sehat
            $table->decimal('mood_positive_pct', 5, 2)->nullable()->after('physical_health_pct');        // % mood positif
            $table->decimal('mood_consistency_pct', 5, 2)->nullable()->after('mood_positive_pct');       // % laporan lengkap mood datang & pulang
            $table->unsignedInteger('total_challenges_recorded')->default(0)->after('mood_consistency_pct'); // total kendala tercatat
            $table->unsignedInteger('total_solutions_recorded')->default(0)->after('total_challenges_recorded'); // total solusi tercatat

            // ── Worksheet ─────────────────────────────────────────────────
            $table->decimal('worksheet_submission_pct', 5, 2)->nullable()->after('total_solutions_recorded'); // % worksheet dikumpulkan
            $table->decimal('worksheet_timeliness_pct', 5, 2)->nullable()->after('worksheet_submission_pct'); // % worksheet tepat waktu
            $table->unsignedInteger('total_worksheets')->default(0)->after('worksheet_timeliness_pct');       // total worksheet dikumpulkan
            $table->unsignedInteger('worksheet_student_count')->default(0)->after('total_worksheets');        // jumlah siswa dapat worksheet
            $table->decimal('worksheet_per_student_avg', 5, 2)->nullable()->after('worksheet_student_count'); // rata-rata worksheet per siswa

            // ── Dokumentasi Kegiatan ──────────────────────────────────────
            $table->decimal('documentation_pct', 5, 2)->nullable()->after('worksheet_per_student_avg');  // % sesi terdokumentasi
            $table->decimal('docs_per_report_avg', 5, 2)->nullable()->after('documentation_pct');        // rata-rata dokumentasi per laporan
            $table->unsignedInteger('documented_weeks')->default(0)->after('docs_per_report_avg');       // minggu dengan dokumentasi

            // ── Siswa ─────────────────────────────────────────────────────
            $table->unsignedInteger('active_student_count')->default(0)->after('documented_weeks');      // siswa aktif ditangani
            $table->unsignedInteger('students_no_report_this_week')->default(0)->after('active_student_count'); // siswa tanpa laporan minggu ini
            $table->decimal('student_positive_progress_pct', 5, 2)->nullable()->after('students_no_report_this_week'); // % siswa dengan perkembangan positif
            $table->decimal('reports_per_student_avg', 5, 2)->nullable()->after('student_positive_progress_pct'); // rata-rata laporan per siswa
        });
    }

    public function down(): void
    {
        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->dropColumn([
                'report_completeness_pct', 'timeliness_score', 'weekly_consistency',
                'longest_streak', 'avg_fill_time_minutes',
                'physical_health_pct', 'mood_positive_pct', 'mood_consistency_pct',
                'total_challenges_recorded', 'total_solutions_recorded',
                'worksheet_submission_pct', 'worksheet_timeliness_pct',
                'total_worksheets', 'worksheet_student_count', 'worksheet_per_student_avg',
                'documentation_pct', 'docs_per_report_avg', 'documented_weeks',
                'active_student_count', 'students_no_report_this_week',
                'student_positive_progress_pct', 'reports_per_student_avg',
            ]);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah is_absent & absent_reason ke daily_reports (kalau belum ada)
        if (!Schema::hasColumn('daily_reports', 'attendance_status')) {
            Schema::table('daily_reports', function (Blueprint $table) {
                $table->enum('attendance_status', ['hadir', 'sakit', 'izin', 'alpha'])
                    ->default('hadir')
                    ->after('date');
            });
        }

        // Tambah field baru ke daily_report_details
        Schema::table('daily_report_details', function (Blueprint $table) {
            // Pencapaian
            $table->string('achievement_note', 500)->nullable()->after('homework_detail');
            $table->enum('achievement_tag', ['first_time', 'improvement', 'consistent'])->nullable()->after('achievement_note');

            // Komunikasi
            $table->enum('communication_mode', ['verbal', 'non_verbal', 'gesture', 'aac'])->nullable()->after('achievement_tag');
            $table->enum('communication_initiative', ['often', 'sometimes', 'rarely'])->nullable()->after('communication_mode');

            // Interaksi sosial
            $table->enum('social_with_teacher', ['responsive', 'needs_encouragement', 'refusing'])->nullable()->after('communication_initiative');
            $table->enum('social_with_peers', ['active', 'passive', 'avoiding'])->nullable()->after('social_with_teacher');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn('attendance_status');
        });

        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->dropColumn([
                'achievement_note',
                'achievement_tag',
                'communication_mode',
                'communication_initiative',
                'social_with_teacher',
                'social_with_peers',
            ]);
        });
    }
};
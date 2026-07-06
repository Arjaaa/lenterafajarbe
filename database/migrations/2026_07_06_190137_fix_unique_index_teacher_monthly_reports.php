<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            // teacher_id dipakai oleh foreign key constraint, jadi harus ada
            // index pengganti dulu sebelum unique index lama di-drop, kalau
            // tidak MySQL akan menolak (error 1553).
            $table->index('teacher_id', 'tmr_teacher_id_index');
        });

        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->dropUnique('teacher_monthly_reports_teacher_id_month_year_unique');
            $table->unique(['teacher_id', 'month', 'year', 'period_start'], 'tmr_teacher_month_year_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->dropUnique('tmr_teacher_month_year_period_unique');
            $table->unique(['teacher_id', 'month', 'year'], 'teacher_monthly_reports_teacher_id_month_year_unique');
        });

        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->dropIndex('tmr_teacher_id_index');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->date('period_start')->nullable()->after('academic_year');
            $table->date('period_end')->nullable()->after('period_start');
            $table->boolean('is_partial')->default(false)->after('period_end');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->dropColumn(['period_start', 'period_end', 'is_partial']);
        });
    }
};
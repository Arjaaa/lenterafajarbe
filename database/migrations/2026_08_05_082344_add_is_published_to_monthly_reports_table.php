<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
        });

        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });

        Schema::table('teacher_monthly_reports', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }
};
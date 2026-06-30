<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['is_absent', 'absent_reason']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->boolean('is_absent')->default(false)->after('date');
            $table->string('absent_reason')->nullable()->after('is_absent');
        });
    }
};
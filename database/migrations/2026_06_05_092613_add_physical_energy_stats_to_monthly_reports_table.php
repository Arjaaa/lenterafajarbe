<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('monthly_reports', function (Blueprint $table) {
        $table->json('physical_energy_arrival_stats')->nullable()->after('physical_condition_end_stats');
        $table->json('physical_energy_end_stats')->nullable()->after('physical_energy_arrival_stats');
    });
}

public function down(): void
{
    Schema::table('monthly_reports', function (Blueprint $table) {
        $table->dropColumn(['physical_energy_arrival_stats', 'physical_energy_end_stats']);
    });
}
};

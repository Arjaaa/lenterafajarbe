<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            // Rename physical_energy_arrival → physical_condition_end
            $table->renameColumn('physical_energy_arrival', 'physical_condition_end');
            $table->renameColumn('physical_energy_other', 'physical_condition_end_other');
        });

        Schema::table('daily_report_classifications', function (Blueprint $table) {
            // Rename physical_energy_category → physical_condition_end_category
            $table->renameColumn('physical_energy_category', 'physical_condition_end_category');
        });
    }

    public function down(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->renameColumn('physical_condition_end', 'physical_energy_arrival');
            $table->renameColumn('physical_condition_end_other', 'physical_energy_other');
        });

        Schema::table('daily_report_classifications', function (Blueprint $table) {
            $table->renameColumn('physical_condition_end_category', 'physical_energy_category');
        });
    }
};
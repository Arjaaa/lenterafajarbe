<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom _other di daily_report_details
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->string('physical_condition_other')->nullable()->after('physical_condition_arrival');
            $table->string('physical_energy_other')->nullable()->after('physical_energy_arrival');
            $table->string('behavior_other')->nullable()->after('behavior');
            $table->string('response_other')->nullable()->after('response');
            $table->string('challenge_other')->nullable()->after('challenge');
        });

        // Tambah flag has_other_note di daily_report_classifications
        Schema::table('daily_report_classifications', function (Blueprint $table) {
            $table->boolean('has_other_note')->default(false)->after('has_homework');
        });
    }

    public function down(): void
    {
        Schema::table('daily_report_details', function (Blueprint $table) {
            $table->dropColumn([
                'physical_condition_other',
                'physical_energy_other',
                'behavior_other',
                'response_other',
                'challenge_other',
            ]);
        });

        Schema::table('daily_report_classifications', function (Blueprint $table) {
            $table->dropColumn('has_other_note');
        });
    }
};
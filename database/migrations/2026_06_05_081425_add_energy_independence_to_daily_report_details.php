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
    Schema::table('daily_report_details', function (Blueprint $table) {
        $table->enum('physical_energy_arrival', ['ceria', 'aktif', 'lelah', 'tenang', 'lainnya'])->nullable()->after('physical_condition_end_other');
        $table->string('physical_energy_arrival_other')->nullable()->after('physical_energy_arrival');
        $table->enum('physical_energy_end', ['ceria', 'aktif', 'lelah', 'tenang', 'lainnya'])->nullable()->after('physical_energy_arrival_other');
        $table->string('physical_energy_end_other')->nullable()->after('physical_energy_end');
        $table->enum('independence', ['mandiri', 'perlu_bantuan', 'sangat_mandiri', 'lainnya'])->nullable()->after('physical_energy_end_other');
        $table->string('independence_other')->nullable()->after('independence');
    });
}

public function down(): void
{
    Schema::table('daily_report_details', function (Blueprint $table) {
        $table->dropColumn([
            'physical_energy_arrival',
            'physical_energy_arrival_other',
            'physical_energy_end',
            'physical_energy_end_other',
            'independence',
            'independence_other',
        ]);
    });
}
};

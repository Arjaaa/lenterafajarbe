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
    Schema::table('daily_report_classifications', function (Blueprint $table) {
        $table->enum('physical_energy_arrival_category', ['positif', 'netral', 'negatif'])->nullable()->after('physical_condition_end_category');
        $table->enum('physical_energy_end_category', ['positif', 'netral', 'negatif'])->nullable()->after('physical_energy_arrival_category');
        $table->enum('independence_category', ['sangat_mandiri', 'mandiri', 'perlu_bantuan'])->nullable()->after('physical_energy_end_category');
    });
}

public function down(): void
{
    Schema::table('daily_report_classifications', function (Blueprint $table) {
        $table->dropColumn([
            'physical_energy_arrival_category',
            'physical_energy_end_category',
            'independence_category',
        ]);
    });
}
};

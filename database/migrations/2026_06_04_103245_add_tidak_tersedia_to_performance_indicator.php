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
    DB::statement("ALTER TABLE teacher_monthly_reports MODIFY COLUMN performance_indicator ENUM('sangat_baik','baik','cukup','kurang','sangat_kurang','tidak_tersedia') NULL");
    DB::statement("ALTER TABLE teacher_annual_reports MODIFY COLUMN annual_performance_indicator ENUM('sangat_baik','baik','cukup','kurang','sangat_kurang','tidak_tersedia') NULL");
}

public function down(): void
{
    DB::statement("ALTER TABLE teacher_monthly_reports MODIFY COLUMN performance_indicator ENUM('sangat_baik','baik','cukup','kurang','sangat_kurang') NULL");
    DB::statement("ALTER TABLE teacher_annual_reports MODIFY COLUMN annual_performance_indicator ENUM('sangat_baik','baik','cukup','kurang','sangat_kurang') NULL");
}
    /**
     * Reverse the migrations.
     */

};

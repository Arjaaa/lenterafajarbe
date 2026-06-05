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
    \DB::statement("ALTER TABLE daily_report_details MODIFY physical_condition_end ENUM('sehat', 'sedikit_lelah', 'kurang_fit', 'mengantuk', 'lainnya') NULL");
}

public function down(): void
{
    \DB::statement("ALTER TABLE daily_report_details MODIFY physical_condition_end ENUM('ceria', 'aktif', 'lelah', 'tenang', 'lainnya') NULL");
}
};

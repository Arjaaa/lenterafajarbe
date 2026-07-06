<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('teacher_monthly_reports', function (Blueprint $table) {
        $table->integer('total_absent_days')->unsigned()->default(0)->after('total_reports_created');
    });
}

public function down()
{
    Schema::table('teacher_monthly_reports', function (Blueprint $table) {
        $table->dropColumn('total_absent_days');
    });
}
};

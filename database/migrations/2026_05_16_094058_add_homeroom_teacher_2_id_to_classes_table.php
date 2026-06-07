<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedBigInteger('homeroom_teacher_2_id')->nullable()->after('homeroom_teacher_id');
            $table->foreign('homeroom_teacher_2_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['homeroom_teacher_2_id']);
            $table->dropColumn('homeroom_teacher_2_id');
        });
    }
};

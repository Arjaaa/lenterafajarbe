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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable(); // Relasi ke anak
            $table->foreignId('shadow_teacher_id')->nullable(); // Relasi ke guru pendamping
            $table->foreignId('therapist_id')->nullable(); // Relasi ke terapis utama
            $table->dateTime('date'); // Waktu laporan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};

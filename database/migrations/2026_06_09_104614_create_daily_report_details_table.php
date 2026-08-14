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
        Schema::create('daily_report_details', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel daily_reports
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');

            // Kondisi & Energi Fisik
            $table->string('physical_condition_arrival')->nullable();
            $table->string('physical_condition_end')->nullable();
            $table->string('physical_energy_arrival')->nullable();
            $table->string('physical_energy_end')->nullable();
            $table->string('independence')->nullable();

            // Mood (Angka 1-5)test 
            $table->integer('mood_arrival')->nullable();
            $table->integer('mood_end')->nullable();

            // Catatan Aktivitas & Tantangan
            $table->string('behavior')->nullable();
            $table->string('behavior_other')->nullable();
            $table->text('activity_notes')->nullable();
            $table->string('response')->nullable();
            $table->string('challenge')->nullable();
            $table->string('challenge_other')->nullable();
            $table->text('solution_notes')->nullable();

            // PR
            $table->boolean('has_homework')->default(false);
            $table->string('homework_detail')->nullable();

            // Foto Cloudinary (Disimpan dalam bentuk JSON Array)
            $table->json('photo_physical')->nullable();
            $table->json('photo_activity')->nullable();
            $table->json('photo_other')->nullable();

            $table->integer('text_length')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_details');
    }
};

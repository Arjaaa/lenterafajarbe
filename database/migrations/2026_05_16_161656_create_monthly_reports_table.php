<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');   // 1–12
            $table->unsignedSmallInteger('year');   // 2026

            // ── Statistik dasar ───────────────────────────────────────────
            $table->unsignedInteger('total_reports')->default(0);
            $table->unsignedInteger('total_homework_days')->default(0);
            $table->unsignedInteger('total_no_homework_days')->default(0);
            $table->unsignedInteger('total_challenges')->default(0);

            // ── Kondisi fisik ─────────────────────────────────────────────
            // Saat datang: Sehat / Sedikit lelah / Mengantuk
            $table->json('physical_condition_stats')->nullable();
            // Saat pulang: Ceria / Biasa / Sedikit membaik
            $table->json('physical_energy_stats')->nullable();

            // ── Mood ──────────────────────────────────────────────────────
            $table->decimal('mood_arrival_avg', 3, 2)->nullable();
            $table->decimal('mood_end_avg', 3, 2)->nullable();
            // Dominan mood datang & pulang — { "Senang": {count, percent}, ... }
            $table->json('mood_arrival_dominant')->nullable();
            $table->json('mood_end_dominant')->nullable();
            // Mood positif vs netral/kurang baik
            $table->json('mood_positive_stats')->nullable();
            // Tren: naik / stabil / turun
            $table->json('mood_trend_stats')->nullable();

            // ── Perilaku (multi-value per hari) ───────────────────────────
            // Kooperatif / Fokus / Antusias / Kurang fokus / Rewel / dll
            $table->json('behavior_stats')->nullable();

            // ── Respon kegiatan ───────────────────────────────────────────
            // Aktif & Antusias / Cukup responsif / Kurang responsif
            $table->json('response_stats')->nullable();

            // ── Kemandirian ───────────────────────────────────────────────
            // Sangat mandiri / Mandiri / Cukup mandiri / Kurang mandiri
            $table->json('independence_stats')->nullable();

            // ── Kendala (multi-value per hari) ────────────────────────────
            // Kurang fokus / Rewel / Mudah terdistraksi / Konflik dengan teman
            $table->json('challenge_stats')->nullable();

            // ── Solusi yang sering diterapkan ─────────────────────────────
            // Pendekatan personal / Waktu istirahat / Diajak bicara
            $table->json('solution_stats')->nullable();

            // ── Kegiatan yang sering diikuti ──────────────────────────────
            // Mengaji / Bermain kelompok / Olahraga / Mewarnai / dll
            $table->json('activity_stats')->nullable();

            // ── Overall score distribution ────────────────────────────────
            $table->json('overall_score_stats')->nullable();

            // ── AI output ─────────────────────────────────────────────────
            $table->text('ai_summary')->nullable();         // Ringkasan perkembangan
            $table->text('ai_attention')->nullable();       // Perhatian khusus
            $table->text('ai_recommendation')->nullable();  // Rekomendasi bulan depan

            $table->enum('status', ['pending', 'generated', 'failed'])->default('pending');
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                  ->unique() // 1 laporan = 1 klasifikasi
                  ->constrained('daily_reports')
                  ->cascadeOnDelete();

            // ── Kondisi Fisik ─────────────────────────────────────────────────
            // positif = sehat/ceria/aktif | netral = sedikit_lelah/tenang | negatif = kurang_fit/mengantuk/lelah
            $table->enum('physical_condition_category', ['positif', 'netral', 'negatif'])->nullable();
            $table->enum('physical_energy_category', ['positif', 'netral', 'negatif'])->nullable();

            // ── Mood ──────────────────────────────────────────────────────────
            // 5=sangat_baik 4=baik 3=cukup 2=kurang 1=sangat_kurang
            $table->enum('mood_arrival_category', ['sangat_baik', 'baik', 'cukup', 'kurang', 'sangat_kurang'])->nullable();
            $table->enum('mood_end_category', ['sangat_baik', 'baik', 'cukup', 'kurang', 'sangat_kurang'])->nullable();
            // Tren mood: naik/stabil/turun dibanding mood_arrival ke mood_end
            $table->enum('mood_trend', ['naik', 'stabil', 'turun'])->nullable();

            // ── Perilaku ──────────────────────────────────────────────────────
            $table->enum('behavior_category', ['positif', 'negatif', 'lainnya'])->nullable();

            // ── Respon ────────────────────────────────────────────────────────
            $table->enum('response_category', ['positif', 'netral', 'negatif', 'lainnya'])->nullable();

            // ── Kendala ───────────────────────────────────────────────────────
            $table->enum('challenge_category', ['ringan', 'sedang', 'berat', 'lainnya'])->nullable();
            $table->boolean('has_challenge')->default(false); // ada kendala atau tidak

            // ── PR / Tugas ────────────────────────────────────────────────────
            $table->boolean('has_homework')->default(false);

            // ── Skor Keseluruhan Hari ─────────────────────────────────────────
            // Dihitung dari semua kategori → untuk grafik & statistik bulanan
            $table->enum('overall_score', ['sangat_baik', 'baik', 'cukup', 'kurang', 'sangat_kurang'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_classifications');
    }
};
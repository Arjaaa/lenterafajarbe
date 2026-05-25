<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel utama laporan harian ────────────────────────────────────────
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            // Salah satu diisi, yang lain null — tergantung siapa yang buat laporan
            $table->foreignId('shadow_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('therapist_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('date');
            $table->timestamps();

            // 1 murid hanya boleh punya 1 laporan per hari
            $table->unique(['student_id', 'date']);
        });

        // ── Detail isi laporan ────────────────────────────────────────────────
        Schema::create('daily_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                  ->constrained('daily_reports')
                  ->cascadeOnDelete();

            // 1. Kondisi Fisik
            $table->enum('physical_condition_arrival', [
                'sehat', 'sedikit_lelah', 'kurang_fit', 'mengantuk', 'lainnya'
            ])->nullable();

            $table->enum('physical_energy_arrival', [
                'ceria', 'aktif', 'lelah', 'tenang', 'lainnya'
            ])->nullable();

            // 2. Mood (1=sangat buruk ... 5=sangat baik)
            $table->unsignedTinyInteger('mood_arrival')->nullable();  // 1-5
            $table->unsignedTinyInteger('mood_end')->nullable();      // 1-5

            // 3. Perilaku
            $table->enum('behavior', [
                'kooperatif', 'fokus', 'aktif', 'mudah_terdistraksi', 'lainnya'
            ])->nullable();

            // 4. Kegiatan Hari Ini
            $table->text('activity_notes')->nullable();

            // 5. Respon Anak
            $table->enum('response', [
                'antusias', 'pasif', 'perlu_arahan', 'perlu_pengawasan', 'lainnya'
            ])->nullable();

            // 6. Kendala & Solusi
            $table->enum('challenge', [
                'kurang_fokus', 'mudah_terdistraksi', 'mood_kurang_stabil', 'sulit_diarahkan', 'lainnya'
            ])->nullable();
            $table->text('solution_notes')->nullable();

            // 7. Tugas / PR
            $table->boolean('has_homework')->default(false);
            $table->text('homework_detail')->nullable();

            // 8. Dokumentasi (path file)
            $table->string('photo_physical')->nullable();
            $table->string('photo_activity')->nullable();
            $table->string('photo_other')->nullable();

            // Untuk analisa AI nanti
            $table->integer('text_length')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_details');
        Schema::dropIfExists('daily_reports');
    }
};
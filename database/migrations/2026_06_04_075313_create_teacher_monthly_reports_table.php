<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');   // 1-12
            $table->unsignedSmallInteger('year');
            $table->string('academic_year', 10);    // "2025/2026"
 
            // ── Statistik Kinerja ─────────────────────────────────────
            $table->unsignedInteger('total_teaching_days')->default(0);   // hari kerja efektif
            $table->unsignedInteger('total_reports_created')->default(0); // laporan dibuat
            $table->unsignedInteger('total_missing_days')->default(0);    // hari kerja - laporan
            $table->decimal('avg_report_length', 8, 2)->nullable();       // rata-rata text_length
 
            // ── Kualitas Laporan ──────────────────────────────────────
            $table->decimal('observation_score', 3, 2)->nullable();       // 1-5
            $table->decimal('analysis_score', 3, 2)->nullable();          // 1-5
            $table->decimal('solution_score', 3, 2)->nullable();          // 1-5
            $table->decimal('completeness_score', 5, 2)->nullable();      // persentase
 
            // ── AI Output ─────────────────────────────────────────────
            $table->json('ai_improvement_areas')->nullable();
            $table->text('ai_performance_summary')->nullable();
 
            // ── Manual (diisi coordinator) ────────────────────────────
            $table->text('coordinator_recommendation')->nullable();
            $table->enum('performance_indicator', [
                'sangat_baik', 'baik', 'cukup', 'kurang', 'sangat_kurang'
            ])->nullable();
 
            $table->enum('status', ['pending', 'generated', 'failed'])->default('pending');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
 
            $table->unique(['teacher_id', 'month', 'year']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('teacher_monthly_reports');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_annual_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('academic_year', 10);    // "2025/2026"
 
            // ── Akumulasi dari monthly ────────────────────────────────
            $table->unsignedInteger('total_teaching_days_year')->default(0);
            $table->unsignedInteger('total_reports_created_year')->default(0);
            $table->unsignedInteger('total_missing_days_year')->default(0);
            $table->decimal('avg_report_length_year', 8, 2)->nullable();
 
            // ── Skor rata-rata tahunan ────────────────────────────────
            $table->decimal('avg_observation_score', 3, 2)->nullable();
            $table->decimal('avg_analysis_score', 3, 2)->nullable();
            $table->decimal('avg_solution_score', 3, 2)->nullable();
            $table->decimal('avg_completeness_score', 5, 2)->nullable();
 
            // ── AI Output ─────────────────────────────────────────────
            $table->text('ai_annual_summary')->nullable();
            $table->json('ai_annual_improvement_areas')->nullable();
 
            // ── Manual (diisi coordinator) ────────────────────────────
            $table->text('coordinator_annual_recommendation')->nullable();
            $table->enum('annual_performance_indicator', [
                'sangat_baik', 'baik', 'cukup', 'kurang', 'sangat_kurang'
            ])->nullable();
 
            $table->enum('status', ['pending', 'generated', 'failed'])->default('pending');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
 
            $table->unique(['teacher_id', 'academic_year']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('teacher_annual_reports');
    }
};
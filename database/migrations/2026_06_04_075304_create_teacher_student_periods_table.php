<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_student_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('academic_year', 10);  // "2025/2026"
            $table->enum('role_type', ['homeroom', 'shadow_pj', 'shadow_teacher', 'therapist']);
            $table->boolean('is_active')->default(true);
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->timestamps();
 
            $table->unique(['teacher_id', 'student_id', 'academic_year', 'role_type'], 'teacher_student_period_unique');
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('teacher_student_periods');
    }
};
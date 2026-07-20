<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherMonthlyReport extends Model
{
    protected $fillable = [
        'teacher_id', 'month', 'year', 'academic_year',
        'period_start', 'period_end', 'is_partial',
        'total_teaching_days', 'total_reports_created',
        'total_absent_days',
        'total_missing_days', 'avg_report_length',
        'observation_score', 'analysis_score',
        'solution_score', 'completeness_score',

        // Laporan harian
        'report_completeness_pct', 'timeliness_score', 'weekly_consistency',
        'longest_streak', 'avg_fill_time_minutes',

        // Kondisi & mood anak
        'physical_health_pct', 'mood_positive_pct', 'mood_consistency_pct',
        'total_challenges_recorded', 'total_solutions_recorded',

        // Worksheet
        'worksheet_submission_pct', 'worksheet_timeliness_pct',
        'total_worksheets', 'worksheet_student_count', 'worksheet_per_student_avg',

        // Dokumentasi
        'documentation_pct', 'docs_per_report_avg', 'documented_weeks',

        // Siswa
        'active_student_count', 'students_no_report_this_week',
        'student_positive_progress_pct', 'reports_per_student_avg',

        // AI & manual
        'ai_improvement_areas', 'ai_performance_summary',
        'coordinator_recommendation', 'performance_indicator',
        'status', 'generated_at',
    ];

    protected $casts = [
        'ai_improvement_areas' => 'array',
        'generated_at'         => 'datetime',
        'period_start'         => 'date:Y-m-d',
        'period_end'           => 'date:Y-m-d',
        'is_partial'           => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
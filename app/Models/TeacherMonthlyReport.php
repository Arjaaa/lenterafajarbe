<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class TeacherMonthlyReport extends Model
{
    protected $fillable = [
        'teacher_id', 'month', 'year', 'academic_year',
        'total_teaching_days', 'total_reports_created',
        'total_missing_days', 'avg_report_length',
        'observation_score', 'analysis_score',
        'solution_score', 'completeness_score',
        'ai_improvement_areas', 'ai_performance_summary',
        'coordinator_recommendation', 'performance_indicator',
        'status', 'generated_at',
    ];
 
    protected $casts = [
        'ai_improvement_areas' => 'array',
        'generated_at'         => 'datetime',
    ];
 
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
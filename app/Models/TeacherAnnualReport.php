<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class TeacherAnnualReport extends Model
{
    protected $fillable = [
        'teacher_id', 'academic_year',
        'total_teaching_days_year', 'total_reports_created_year',
        'total_missing_days_year', 'avg_report_length_year',
        'avg_observation_score', 'avg_analysis_score',
        'avg_solution_score', 'avg_completeness_score',
        'ai_annual_summary', 'ai_annual_improvement_areas',
        'coordinator_annual_recommendation', 'annual_performance_indicator',
        'status', 'generated_at',
    ];
 
    protected $casts = [
        'ai_annual_improvement_areas' => 'array',
        'generated_at'                => 'datetime',
    ];
 
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

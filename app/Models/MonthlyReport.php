<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'month', 'year',
        'total_reports', 'total_homework_days', 'total_no_homework_days', 'total_challenges',
        'physical_condition_stats', 'physical_condition_end_stats',
        'mood_arrival_avg', 'mood_end_avg', 'mood_trend_stats',
        'behavior_stats', 'response_stats', 'challenge_stats',
        'overall_score_stats',
        'ai_summary', 'ai_attention', 'ai_recommendation',
        'status', 'generated_at',
    ];

    protected $casts = [
        'physical_condition_stats'     => 'array',
        'physical_condition_end_stats' => 'array',
        'mood_trend_stats'             => 'array',
        'behavior_stats'               => 'array',
        'response_stats'               => 'array',
        'challenge_stats'              => 'array',
        'overall_score_stats'          => 'array',
        'generated_at'                 => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
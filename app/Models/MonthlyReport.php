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
        'physical_energy_arrival_stats', 'physical_energy_end_stats',
        'coordinator_note',
        'independence_stats',
        'mood_arrival_avg', 'mood_end_avg', 'mood_trend_stats',
        'mood_arrival_dominant', 'mood_end_dominant', 'mood_positive_stats',
        'behavior_stats', 'response_stats', 'challenge_stats',
        'activity_stats', 'solution_stats',
        'overall_score_stats',
        'achievement_tag_stats',
        'communication_mode_stats', 'communication_initiative_stats',
        'social_with_teacher_stats', 'social_with_peers_stats',
        'attendance_stats',
        'ai_summary', 'ai_attention', 'ai_recommendation',
        // ── AI field baru ────────────────────────────────────────────────────
        'ai_headline', 'ai_headline_emoji', 'ai_attention_trend', 'ai_attention_note',
        // ────────────────────────────────────────────────────────────────────
        'status', 'generated_at',
    ];

    protected $casts = [
        'physical_condition_stats'       => 'array',
        'physical_condition_end_stats'   => 'array',
        'physical_energy_arrival_stats'  => 'array',
        'physical_energy_end_stats'      => 'array',
        'independence_stats'             => 'array',
        'mood_trend_stats'               => 'array',
        'mood_arrival_dominant'          => 'array',
        'mood_end_dominant'              => 'array',
        'mood_positive_stats'            => 'array',
        'behavior_stats'                 => 'array',
        'response_stats'                 => 'array',
        'challenge_stats'                => 'array',
        'activity_stats'                 => 'array',
        'solution_stats'                 => 'array',
        'overall_score_stats'            => 'array',
        'achievement_tag_stats'          => 'array',
        'communication_mode_stats'       => 'array',
        'communication_initiative_stats' => 'array',
        'social_with_teacher_stats'      => 'array',
        'social_with_peers_stats'        => 'array',
        'attendance_stats'               => 'array',
        'generated_at'                   => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'physical_condition_category',
        'physical_condition_end_category',
        'mood_arrival_category',
        'mood_end_category',
        'mood_trend',
        'behavior_category',
        'response_category',
        'challenge_category',
        'has_challenge',
        'has_homework',
        'has_other_note',
        'overall_score',
    ];

    protected $casts = [
        'has_challenge'  => 'boolean',
        'has_homework'   => 'boolean',
        'has_other_note' => 'boolean',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
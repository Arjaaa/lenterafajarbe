<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'physical_condition_arrival',
        'physical_condition_other',
        'physical_condition_end',
        'physical_condition_end_other',
        'physical_energy_arrival',
        'physical_energy_arrival_other',
        'physical_energy_end',
        'physical_energy_end_other',
        'independence',
        'independence_other',
        'mood_arrival',
        'mood_end',
        'behavior',
        'behavior_other',
        'activity_notes',
        'response',
        'response_other',
        'challenge',
        'challenge_other',
        'solution_notes',
        'has_homework',
        'homework_detail',
        'photo_physical',  // now JSON array
        'photo_activity',  // now JSON array
        'photo_other',     // now JSON array
        'text_length',
    ];

    protected $casts = [
        'has_homework'   => 'boolean',
        'mood_arrival'   => 'integer',
        'mood_end'       => 'integer',
        'text_length'    => 'integer',
        'photo_physical' => 'array',  // ← ubah
        'photo_activity' => 'array',  // ← ubah
        'photo_other'    => 'array',  // ← ubah
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
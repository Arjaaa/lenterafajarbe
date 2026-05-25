<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',

        // Kondisi Fisik Saat Datang
        'physical_condition_arrival',
        'physical_condition_other',

        // Kondisi Fisik Saat Pulang
        'physical_condition_end',
        'physical_condition_end_other',

        // Mood
        'mood_arrival',
        'mood_end',

        // Perilaku
        'behavior',
        'behavior_other',

        // Kegiatan
        'activity_notes',

        // Respon
        'response',
        'response_other',

        // Kendala & Solusi
        'challenge',
        'challenge_other',
        'solution_notes',

        // PR
        'has_homework',
        'homework_detail',

        // Dokumentasi
        'photo_physical',
        'photo_activity',
        'photo_other',

        'text_length',
    ];

    protected $casts = [
        'has_homework' => 'boolean',
        'mood_arrival' => 'integer',
        'mood_end'     => 'integer',
        'text_length'  => 'integer',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
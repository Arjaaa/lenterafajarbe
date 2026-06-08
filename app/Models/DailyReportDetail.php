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
        'photo_physical',
        'photo_activity',
        'photo_other',
        'text_length',
    ];

    protected $casts = [
        'has_homework'   => 'boolean',
        'mood_arrival'   => 'integer',
        'mood_end'       => 'integer',
        'text_length'    => 'integer',
        'photo_physical' => 'array',
        'photo_activity' => 'array',
        'photo_other'    => 'array',
    ];

    protected $appends = [
        'physical_condition_arrival_label',
        'physical_condition_end_label',
        'physical_energy_arrival_label',
        'physical_energy_end_label',
        'independence_label',
        'behavior_label',
        'response_label',
        'challenge_label',
    ];

    private function formatLabel(?string $value): ?string
    {
        if (!$value) return null;
        return ucfirst(str_replace('_', ' ', $value));
    }

    public function getPhysicalConditionArrivalLabelAttribute(): ?string
    {
        return $this->formatLabel($this->physical_condition_arrival);
    }

    public function getPhysicalConditionEndLabelAttribute(): ?string
    {
        return $this->formatLabel($this->physical_condition_end);
    }

    public function getPhysicalEnergyArrivalLabelAttribute(): ?string
    {
        return $this->formatLabel($this->physical_energy_arrival);
    }

    public function getPhysicalEnergyEndLabelAttribute(): ?string
    {
        return $this->formatLabel($this->physical_energy_end);
    }

    public function getIndependenceLabelAttribute(): ?string
    {
        return $this->formatLabel($this->independence);
    }

    public function getBehaviorLabelAttribute(): ?string
    {
        return $this->formatLabel($this->behavior);
    }

    public function getResponseLabelAttribute(): ?string
    {
        return $this->formatLabel($this->response);
    }

    public function getChallengeLabelAttribute(): ?string
    {
        return $this->formatLabel($this->challenge);
    }

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'shadow_teacher_id',
        'therapist_id',
        'date',
        'is_absent',
        'absent_reason',
        'attendance_status', // ← tambahan
    ];

    protected $casts = [
        'date'      => 'date',
        'is_absent' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function shadowTeacher()
    {
        return $this->belongsTo(User::class, 'shadow_teacher_id');
    }

    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function detail()
    {
        return $this->hasOne(DailyReportDetail::class);
    }

    public function classification()
    {
        return $this->hasOne(DailyReportClassification::class);
    }

    public function getCreatedByAttribute()
    {
        return $this->shadowTeacher ?? $this->therapist;
    }
}
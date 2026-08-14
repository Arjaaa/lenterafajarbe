<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Membiarkan semua kolom bisa diisi kecuali ID

    // Relasi ke detail laporan
    public function detail()
    {
        // Asumsi nama model detailnya adalah DailyReportDetail
        return $this->hasOne(DailyReportDetail::class, 'daily_report_id');
    }

    // Relasi ke data Anak
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Relasi ke Guru (Terapis)
    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    // Relasi ke Guru Pendamping (Shadow Teacher)
    public function shadowTeacher()
    {
        return $this->belongsTo(User::class, 'shadow_teacher_id');
    }
}
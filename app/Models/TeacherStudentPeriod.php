<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class TeacherStudentPeriod extends Model
{
    protected $fillable = [
        'teacher_id', 'student_id', 'academic_year',
        'role_type', 'is_active', 'started_at', 'ended_at',
    ];
 
    protected $casts = [
        'is_active'  => 'boolean',
        'started_at' => 'date',
        'ended_at'   => 'date',
    ];
 
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
 
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
        'birth_date',
        'gender',
        'school_name',
        'address',
        'special_needs',
        'diagnosis_notes',
        'parent_id',
        'parent_phone',
        'father_name',
        'mother_name',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];




    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_students', 'student_id', 'class_id');
    }

    public function shadowGroup()
    {
        return $this->hasOne(ShadowGroup::class);
    }

    public function oneOnOneGroup()
    {
        return $this->hasOne(OneOnOneGroup::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }
}
 
// class Student extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'name',
//         'nis',
//         'birth_date',
//         'parent_id' 
//     ];

//     public function parent()
//     {
//         return $this->belongsTo(User::class, 'parent_id');
//     }
// }
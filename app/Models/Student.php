<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Student extends Model
{
    use HasFactory;
 
    protected $fillable = ['name'];
 
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
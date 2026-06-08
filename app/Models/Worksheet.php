<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worksheet extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'uploaded_by',
        'student_id',
        'title',
        'description',
        'category',
        'file_url',
        'file_type',
        'original_filename',
        'status',
    ];
 
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
 
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
 

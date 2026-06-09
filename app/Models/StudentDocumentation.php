<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocumentation extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'student_id',
        'uploaded_by',
        'media_type',
        'media_url',
        'thumbnail_url',
        'title',
        'description',
        'activity_date',
    ];
 
    protected $casts = [
        'media_url'     => 'array',
        'thumbnail_url' => 'array',
        'activity_date' => 'date',
        'media_types'   => 'array',
    ];
 
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
 
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

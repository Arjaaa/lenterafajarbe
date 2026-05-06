<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShadowGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'student_id',
        'pic_id',
        'partner_id',
        'school_name',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }


    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
}
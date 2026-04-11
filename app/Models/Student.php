<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nis',
        'birth_date',
        'parent_id' 
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
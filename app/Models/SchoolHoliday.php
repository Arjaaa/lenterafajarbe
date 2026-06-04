<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SchoolHoliday extends Model
{
    protected $fillable = ['date', 'name', 'type', 'created_by'];
 
    protected $casts = [
        'date' => 'date',
    ];
 
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
 
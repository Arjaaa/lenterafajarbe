<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type',
        'start_date', 'end_date', 'is_active',
        'created_by', 'media_urls',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
        'media_urls' => 'array',
    ];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:sP'); // WIB, format: 2026-07-19T13:23:38+07:00
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'address',
        'gender',
        'is_active',
        'school_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];


    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function isCoordinatorMain(): bool
    {
        return $this->role === 'coordinator_main';
    }

    public function isCoordinator(): bool
    {
        return in_array($this->role, [
            'coordinator_main',
            'coordinator_therapist',
            'coordinator_shadow',
            'coordinator_wil',
        ]);
    }

    public function isShadowTeacher(): bool
    {
        return in_array($this->role, ['shadow_pj', 'shadow_teacher']);
    }

    public function isTherapist(): bool
    {
        return in_array($this->role, ['therapist_homeroom', 'therapist']);
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }
}
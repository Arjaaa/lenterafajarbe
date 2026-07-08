<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    
public function register(array $data): array
{
    $user = User::create([
        'name'      => $data['name'],
        'email'     => $data['email'],
        'password'  => Hash::make($data['password']),
        'role'      => null,
        'phone'     => $data['phone']   ?? null,
        'gender'    => $data['gender']  ?? null,
        'address'   => $data['address'] ?? null,
        'is_active' => false,
    ]);

    return ['user' => $user];
}
}
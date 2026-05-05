<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    
    public function register(array $data): array
{
    $user = User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => Hash::make($data['password']),
        'role'     => $data['role'],
        'phone'    => $data['phone'] ?? null,
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return ['user' => $user, 'token' => $token];
}
}
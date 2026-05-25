<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    
public function register(array $data): array
{
    $user = new User();

    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->password = Hash::make($data['password']);
    $user->role = $data['role'];
    $user->phone = $data['phone'] ?? null;
    $user->gender = $data['gender'] ?? null;
    $user->address = $data['address'] ?? null;

    $user->save();

    $token = $user->createToken('api-token')->plainTextToken;

    return [
        'user' => $user,
        'token' => $token
    ];
}
}
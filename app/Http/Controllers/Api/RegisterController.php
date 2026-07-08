<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

public function register(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:100',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'phone'    => 'nullable|string|max:20',
        'gender'   => 'nullable|string|in:male,female',
        'address'  => 'nullable|string|max:255',
    ]);

    $result = $this->registerService->register($request->all());

    return response()->json([
        'message' => 'Register berhasil. Tunggu koordinator mengaktifkan akun Anda.',
        'user'    => $result['user'],
    ], 201);
}
}
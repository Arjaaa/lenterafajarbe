<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RegisterService;

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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $result = $this->registerService->register($request->all());

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $result['user'],
            'token' => $result['token']
        ], 201);
    }
}

// tanya AI, ini buat register data yg diisi apa aja, buat teacher & ortu : jawaban AI minimal name, email, password dan role
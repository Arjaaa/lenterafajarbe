<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Wajib dipanggil untuk nembak API Arza

class AuthController extends Controller
{
    // 1. Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan ini sesuai dengan letak file blade-mu
    }

    // 2. Memproses data form saat tombol Masuk diklik
    public function login(Request $request)
    {
        // Validasi inputan form
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tembak API Arza (Login)
        $apiUrl = env('API_BASE_URL', 'http://202.10.44.2/api') . '/login';

        $response = Http::post($apiUrl, [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Jika API VPS Arza bilang sukses (200 OK)
        if ($response->successful()) {
            $data = $response->object();

            // Simpan Token dari Arza ke dalam Saku (Session)
            session([
                'api_token' => $data->token ?? $data->access_token ?? null,
                'user_data' => $data->user ?? null,
                'is_logged_in' => true
            ]);

            // Arahkan ke Dashboard
            return redirect()->route('koor.dashboard');
        }

        // Kalau gagal (password salah/email salah)
        return back()->withErrors([
            'email' => 'Email atau password salah, atau server tidak merespon.',
        ])->withInput();
    }

    // 3. Memproses Logout
    public function logout()
    {
        // Buang Token dari saku
        session()->flush();
        return redirect()->route('login');
    }
}
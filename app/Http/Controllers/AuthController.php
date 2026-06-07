<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * 1. Menampilkan halaman form login
     */
    public function showLoginForm()
    {
        // Kalau user ternyata udah login, jangan biarin dia ke halaman login lagi
        // Langsung usir balik ke dashboard!
        if (Auth::check()) {
            return redirect()->route('koor.dashboard');
        }

        return view('auth.login'); // Kita akan buat file blade ini di Step 2
    }

    /**
     * 2. Proses mengecek Email & Password saat tombol "Masuk" diklik
     */
    public function login(Request $request)
    {
        // Validasi inputan form (pastikan email & password wajib diisi)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Auth::attempt ini adalah agen rahasia Laravel yang ngecek ke database
        if (Auth::attempt($credentials)) {
            // Kalau cocok!
            $request->session()->regenerate(); // Bikin sesi baru biar aman dari hacker

            // Arahkan ke dashboard koordinator
            return redirect()->intended(route('koor.dashboard'))
                ->with('success', 'Selamat datang kembali!');
        }

        // Kalau gagal (email/password salah), tendang balik ke halaman login bawa pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang kamu masukkan salah.',
        ])->onlyInput('email'); // Biar email yang tadi diketik nggak hilang
    }

    /**
     * 3. Proses Logout (Keluar)
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Cabut aksesnya

        $request->session()->invalidate(); // Hancurkan sesinya
        $request->session()->regenerateToken(); // Bikin token baru demi keamanan

        // Lempar balik ke halaman login
        return redirect()->route('login');
    }
}
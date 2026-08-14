<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ngecek: Kalau di sakunya SUDAH ada token, ngapain di halaman login? 
        // Langsung lempar aja ke Dashboard!
        if (session()->has('api_token')) {
            return redirect()->route('koor.dashboard');
        }

        // Kalau BELUM punya token, silakan lewat buat ngisi form Login.
        return $next($request);
    }
}
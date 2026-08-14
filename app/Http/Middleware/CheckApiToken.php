<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('api_token')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi Anda telah habis atau Anda belum login.'
            ]);
        }

        return $next($request);
    }
}
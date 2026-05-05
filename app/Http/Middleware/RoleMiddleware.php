<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    private const GROUPS = [
        'coordinator' => [
            'coordinator_main',
            'coordinator_therapist',
            'coordinator_shadow',
            'coordinator_wil',
        ],
        'teacher' => [
            'shadow_pj',
            'shadow_teacher',
            'therapist_homeroom',
            'therapist',
        ],
    ];

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Expand shorthand grup ke role konkret
        $expanded = [];
        foreach ($roles as $r) {
            if (isset(self::GROUPS[$r])) {
                $expanded = array_merge($expanded, self::GROUPS[$r]);
            } else {
                $expanded[] = $r;
            }
        }

        if (!in_array($user->role, $expanded)) {
            return response()->json(['message' => 'Forbidden - Role tidak diizinkan'], 403);
        }

        return $next($request);
    }
}
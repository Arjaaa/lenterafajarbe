<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // GET /api/users
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name/email
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->get()->map(fn($u) => [
            'id'        => $u->id,
            'name'      => $u->name,
            'email'     => $u->email,
            'phone'     => $u->phone,
            'role'      => $u->role,
            'is_active' => $u->is_active,
            'created_at'=> $u->created_at,
        ]);

        return response()->json([
            'success' => true,
            'total'   => $users->count(),
            'data'    => $users,
        ]);
    }

    // GET /api/users/{id}
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'role'      => $user->role,
                'is_active' => $user->is_active,
                'created_at'=> $user->created_at,
            ],
        ]);
    }

    // PUT /api/users/{id}/activate
    public function activate($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun sudah aktif.',
            ], 422);
        }

        $user->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil diaktifkan.",
            'data'    => ['id' => $user->id, 'name' => $user->name, 'is_active' => true],
        ]);
    }

    // PUT /api/users/{id}/deactivate
    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun sudah nonaktif.',
            ], 422);
        }

        $user->update(['is_active' => false]);

        // Hapus semua token aktif agar tidak bisa login lagi
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil dinonaktifkan.",
            'data'    => ['id' => $user->id, 'name' => $user->name, 'is_active' => false],
        ]);
    }

    // PUT /api/users/{id}/role
    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => ['required', Rule::in([
                'coordinator_main',
                'coordinator_therapist',
                'coordinator_shadow',
                'coordinator_wil',
                'shadow_pj',
                'shadow_teacher',
                'therapist_homeroom',
                'therapist',
                'parent',
            ])],
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => "Role {$user->name} berhasil diubah dari {$oldRole} ke {$request->role}.",
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'old_role' => $oldRole ?? 'tidak punya',
                'new_role' => $request->role,
            ],
        ]);
    }
}
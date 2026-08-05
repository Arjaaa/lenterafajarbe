<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
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

        // NEW: Filter therapist_homeroom yang belum jadi wali kelas manapun
        // Dipakai buat dropdown "Pilih Wali Kelas" di form tambah/edit kelas
        if ($request->boolean('available_only') && $request->role === 'therapist_homeroom') {
            $assignedTeacherIds = ClassRoom::whereNotNull('homeroom_teacher_id')
                ->pluck('homeroom_teacher_id');

            // Kalau lagi edit kelas, guru yang SEKARANG jadi wali kelas kelas ini
            // harus tetep muncul di dropdown (jangan ikut ke-exclude)
            if ($request->filled('except_class_id')) {
                $currentClass = ClassRoom::find($request->except_class_id);
                if ($currentClass && $currentClass->homeroom_teacher_id) {
                    $assignedTeacherIds = $assignedTeacherIds->reject(
                        fn($id) => $id == $currentClass->homeroom_teacher_id
                    );
                }
            }

            $query->whereNotIn('id', $assignedTeacherIds);
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

        $message = $oldRole
            ? "Role {$user->name} berhasil diubah dari {$oldRole} ke {$request->role}."
            : "Role {$user->name} berhasil ditambahkan sebagai {$request->role}.";

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'old_role' => $oldRole,
                'new_role' => $request->role,
            ],
        ]);
    }

    // PUT /api/users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'sometimes|string|max:100',
            'email'     => 'sometimes|email|unique:users,email,' . $user->id,
            'password'  => 'sometimes|string|min:6',
            'phone'     => 'sometimes|nullable|string|max:20',
            'gender'    => 'sometimes|nullable|string|in:male,female',
            'address'   => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'role'      => ['sometimes', 'nullable', Rule::in([
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

        $data = $request->only(['name', 'email', 'phone', 'gender', 'address', 'is_active', 'role']);

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            // Password diganti → paksa logout dari semua device
            $user->tokens()->delete();
        }

        if (array_key_exists('is_active', $data) && $data['is_active'] === false && $user->is_active) {
            $user->tokens()->delete();
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => "Data {$user->name} berhasil diperbarui.",
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'gender'    => $user->gender,
                'address'   => $user->address,
                'role'      => $user->role,
                'is_active' => $user->is_active,
            ],
        ]);
    }

    // DELETE /api/users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Hapus semua token aktif supaya langsung ter-logout
        $user->tokens()->delete();

        $user->delete(); // soft delete, bukan beneran hilang dari DB

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil dihapus.",
        ]);
    }

    // PUT /api/users/{id}/restore
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil dipulihkan.",
            'data'    => ['id' => $user->id, 'name' => $user->name],
        ]);
    }
}
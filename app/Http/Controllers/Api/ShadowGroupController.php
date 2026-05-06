<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShadowGroup;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class ShadowGroupController extends Controller
{

    public function index()
    {
        $groups = ShadowGroup::with([
            'student:id,name',
            'pic:id,name,role',
            'partner:id,name,role',
        ])->latest()->get();

        return response()->json($groups);
    }

    public function show($id)
    {
        $group = ShadowGroup::with([
            'student:id,name',
            'pic:id,name,role',
            'partner:id,name,role',
        ])->findOrFail($id);

        return response()->json($group);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'student_name' => 'required|string|max:100',
            'pic_id'       => 'required|exists:users,id',
            'partner_id'   => 'required|exists:users,id',
            'school_name'  => 'required|string|max:150',
        ]);

        $pic = User::findOrFail($request->pic_id);
        if ($pic->role !== 'shadow_pj') {
            return response()->json([
                'message' => 'Penanggung jawab harus memiliki role shadow_pj.',
            ], 422);
        }

        $partner = User::findOrFail($request->partner_id);
        if ($partner->role !== 'shadow_teacher') {
            return response()->json([
                'message' => 'Partner harus memiliki role shadow_teacher.',
            ], 422);
        }

        $student = Student::create(['name' => $request->student_name]);

        $group = ShadowGroup::create([
            'name'        => $request->name,
            'student_id'  => $student->id,
            'pic_id'      => $request->pic_id,
            'partner_id'  => $request->partner_id,
            'school_name' => $request->school_name,
        ]);

        return response()->json([
            'message' => 'Group shadow teacher berhasil dibuat.',
            'group'   => $group->load(['student:id,name', 'pic:id,name,role', 'partner:id,name,role']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $group = ShadowGroup::findOrFail($id);

        $request->validate([
            'name'         => 'sometimes|string|max:100',
            'student_name' => 'sometimes|string|max:100',
            'pic_id'       => 'sometimes|exists:users,id',
            'partner_id'   => 'sometimes|exists:users,id',
            'school_name'  => 'sometimes|string|max:150',
        ]);

        if ($request->has('pic_id')) {
            $pic = User::findOrFail($request->pic_id);
            if ($pic->role !== 'shadow_pj') {
                return response()->json([
                    'message' => 'Penanggung jawab harus memiliki role shadow_pj.',
                ], 422);
            }
        }

        if ($request->has('partner_id')) {
            $partner = User::findOrFail($request->partner_id);
            if ($partner->role !== 'shadow_teacher') {
                return response()->json([
                    'message' => 'Partner harus memiliki role shadow_teacher.',
                ], 422);
            }
        }

        if ($request->has('student_name')) {
            $group->student->update(['name' => $request->student_name]);
        }

        $group->update($request->only('name', 'pic_id', 'partner_id', 'school_name'));

        return response()->json([
            'message' => 'Group shadow teacher berhasil diupdate.',
            'group'   => $group->load(['student:id,name', 'pic:id,name,role', 'partner:id,name,role']),
        ]);
    }

    public function destroy($id)
    {
        $group = ShadowGroup::findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Group shadow teacher berhasil dihapus.']);
    }
}
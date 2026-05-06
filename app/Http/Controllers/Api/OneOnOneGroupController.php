<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OneOnOneGroup;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class OneOnOneGroupController extends Controller
{

    public function index()
    {
        $groups = OneOnOneGroup::with([
            'student:id,name',
            'teacher:id,name,role',
        ])->latest()->get();

        return response()->json($groups);
    }

    public function show($id)
    {
        $group = OneOnOneGroup::with([
            'student:id,name',
            'teacher:id,name,role',
        ])->findOrFail($id);

        return response()->json($group);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'student_name' => 'required|string|max:100',
            'teacher_id'   => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->teacher_id);
        if ($teacher->role !== 'therapist') {
            return response()->json([
                'message' => 'Guru one on one harus memiliki role therapist.',
            ], 422);
        }

        $student = Student::create(['name' => $request->student_name]);

        $group = OneOnOneGroup::create([
            'name'       => $request->name,
            'student_id' => $student->id,
            'teacher_id' => $request->teacher_id,
        ]);

        return response()->json([
            'message' => 'Group one on one berhasil dibuat.',
            'group'   => $group->load(['student:id,name', 'teacher:id,name,role']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $group = OneOnOneGroup::findOrFail($id);

        $request->validate([
            'name'         => 'sometimes|string|max:100',
            'student_name' => 'sometimes|string|max:100',
            'teacher_id'   => 'sometimes|exists:users,id',
        ]);

        if ($request->has('teacher_id')) {
            $teacher = User::findOrFail($request->teacher_id);
            if ($teacher->role !== 'therapist') {
                return response()->json([
                    'message' => 'Guru one on one harus memiliki role therapist.',
                ], 422);
            }
        }

        if ($request->has('student_name')) {
            $group->student->update(['name' => $request->student_name]);
        }

        $group->update($request->only('name', 'teacher_id'));

        return response()->json([
            'message' => 'Group one on one berhasil diupdate.',
            'group'   => $group->load(['student:id,name', 'teacher:id,name,role']),
        ]);
    }
    public function destroy($id)
    {
        $group = OneOnOneGroup::findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Group one on one berhasil dihapus.']);
    }
}
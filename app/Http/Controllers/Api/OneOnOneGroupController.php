<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OneOnOneGroup;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class OneOnOneGroupController extends Controller
{
    // GET /api/one-on-one-groups
    public function index(Request $request)
    {
        $query = OneOnOneGroup::with([
            'student:id,name,photo,gender,special_needs',
            'student.parent:id,name',
            'teacher:id,name,role',
        ])->latest();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $groups = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $groups->items(),
            'meta'    => [
                'current_page' => $groups->currentPage(),
                'last_page'    => $groups->lastPage(),
                'total'        => $groups->total(),
                'per_page'     => $groups->perPage(),
            ],
        ]);
    }

    // GET /api/one-on-one-groups/{id}
    public function show($id)
    {
        $group = OneOnOneGroup::with([
            'student:id,name,photo,gender,special_needs,birth_date,address,diagnosis_notes',
            'student.parent:id,name,phone,email',
            'teacher:id,name,role,phone',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'      => $group->id,
                'name'    => $group->name,
                'teacher' => $group->teacher ? [
                    'id'    => $group->teacher->id,
                    'name'  => $group->teacher->name,
                    'role'  => $group->teacher->role,
                    'phone' => $group->teacher->phone,
                ] : null,
                'student' => $group->student ? [
                    'id'             => $group->student->id,
                    'name'           => $group->student->name,
                    'photo'          => $group->student->photo,
                    'gender'         => $group->student->gender,
                    'special_needs'  => $group->student->special_needs,
                    'birth_date'     => $group->student->birth_date?->toDateString(),
                    'address'        => $group->student->address,
                    'diagnosis_notes'=> $group->student->diagnosis_notes,
                    'parent'         => $group->student->parent ? [
                        'id'    => $group->student->parent->id,
                        'name'  => $group->student->parent->name,
                        'phone' => $group->student->parent->phone,
                        'email' => $group->student->parent->email,
                    ] : null,
                ] : null,
            ],
        ]);
    }

    // POST /api/one-on-one-groups
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->teacher_id);
        if ($teacher->role !== 'therapist') {
            return response()->json([
                'message' => 'Guru one on one harus memiliki role therapist.',
            ], 422);
        }

        $group = OneOnOneGroup::create([
            'name'       => $request->name,
            'student_id' => $request->student_id,
            'teacher_id' => $request->teacher_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelas 1 on 1 berhasil dibuat.',
            'data'    => $group->load([
                'student:id,name,photo,gender,special_needs',
                'student.parent:id,name',
                'teacher:id,name,role',
            ]),
        ], 201);
    }

    // PUT /api/one-on-one-groups/{id}
    public function update(Request $request, $id)
    {
        $group = OneOnOneGroup::findOrFail($id);

        $request->validate([
            'name'       => 'sometimes|string|max:100',
            'teacher_id' => 'sometimes|exists:users,id',
        ]);

        if ($request->has('teacher_id')) {
            $teacher = User::findOrFail($request->teacher_id);
            if ($teacher->role !== 'therapist') {
                return response()->json([
                    'message' => 'Guru one on one harus memiliki role therapist.',
                ], 422);
            }
        }

        $group->update($request->only('name', 'teacher_id'));

        return response()->json([
            'success' => true,
            'message' => 'Kelas 1 on 1 berhasil diupdate.',
            'data'    => $group->load([
                'student:id,name,photo,gender,special_needs',
                'student.parent:id,name',
                'teacher:id,name,role',
            ]),
        ]);
    }

    // PUT /api/one-on-one-groups/{id}/student
    public function changeStudent(Request $request, $id)
    {
        $group = OneOnOneGroup::findOrFail($id);

        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $group->update(['student_id' => $request->student_id]);

        return response()->json([
            'success' => true,
            'message' => 'Siswa terapi berhasil diganti.',
            'data'    => $group->load([
                'student:id,name,photo,gender,special_needs',
                'student.parent:id,name',
                'teacher:id,name,role',
            ]),
        ]);
    }

    // DELETE /api/one-on-one-groups/{id}
    public function destroy($id)
    {
        $group = OneOnOneGroup::findOrFail($id);
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas 1 on 1 berhasil dihapus.',
        ]);
    }
}
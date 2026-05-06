<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with(['homeroomTeacher:id,name,role', 'students:id,name'])
            ->latest()
            ->get();

        return response()->json($classes);
    }

    public function show($id)
    {
        $class = ClassRoom::with(['homeroomTeacher:id,name,role', 'students:id,name'])
            ->findOrFail($id);

        return response()->json($class);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:100',
            'homeroom_teacher_id' => 'required|exists:users,id',
            'students'            => 'nullable|array',
            'students.*.name'     => 'required|string|max:100',
        ]);

        $teacher = User::findOrFail($request->homeroom_teacher_id);
        if ($teacher->role !== 'therapist_homeroom') {
            return response()->json([
                'message' => 'Wali kelas harus memiliki role therapist_homeroom.',
            ], 422);
        }

        $class = ClassRoom::create([
            'name'                => $request->name,
            'homeroom_teacher_id' => $request->homeroom_teacher_id,
        ]);

        if ($request->has('students')) {
            foreach ($request->students as $studentData) {
                $student = Student::create(['name' => $studentData['name']]);
                $class->students()->attach($student->id);
            }
        }

        return response()->json([
            'message' => 'Kelas berhasil dibuat.',
            'class'   => $class->load(['homeroomTeacher:id,name,role', 'students:id,name']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);

        $request->validate([
            'name'                => 'sometimes|string|max:100',
            'homeroom_teacher_id' => 'sometimes|exists:users,id',
        ]);

        if ($request->has('homeroom_teacher_id')) {
            $teacher = User::findOrFail($request->homeroom_teacher_id);
            if ($teacher->role !== 'therapist_homeroom') {
                return response()->json([
                    'message' => 'Wali kelas harus memiliki role therapist_homeroom.',
                ], 422);    
            }
        }

        $class->update($request->only('name', 'homeroom_teacher_id'));

        return response()->json([
            'message' => 'Kelas berhasil diupdate.',
            'class'   => $class->load(['homeroomTeacher:id,name,role', 'students:id,name']),
        ]);
    }

    public function destroy($id)
    {
        $class = ClassRoom::findOrFail($id);
        $class->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }

    public function addStudent(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $student = Student::create(['name' => $request->name]);
        $class->students()->attach($student->id);

        return response()->json([
            'message' => 'Murid berhasil ditambahkan.',
            'student' => $student,
        ], 201);
    }

public function updateStudent(Request $request, $id, $studentId)
{
    $class = ClassRoom::findOrFail($id);
    $isMember = $class->students()->where('student_id', $studentId)->exists();
    if (!$isMember) {
        return response()->json([
            'message' => 'Murid tidak ditemukan di kelas ini.',
        ], 404);
    }

    $request->validate([
        'name' => 'required|string|max:100',
    ]);

    $student = Student::findOrFail($studentId);
    $student->update(['name' => $request->name]);

    return response()->json([
        'message' => 'Data murid berhasil diupdate.',
        'student' => $student,
    ]);
}
    public function removeStudent($id, $studentId)
    {
        $class = ClassRoom::findOrFail($id);
        $class->students()->detach($studentId);

        return response()->json(['message' => 'Murid berhasil dihapus dari kelas.']);
    }
}
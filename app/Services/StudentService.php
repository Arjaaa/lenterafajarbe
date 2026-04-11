<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentService
{
    public function getAll()
    {
        $user = Auth::user();

        if ($user->role === 'parent') {
            return Student::where('parent_id', $user->id)->get();
        }
        return Student::all();
    }

    public function create($data)
    {
        return Student::create($data);
    }

    public function find($id)
    {
        $user = Auth::user();
        $student = Student::findOrFail($id);

        if ($user->role === 'parent' && $student->parent_id !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }

        return $student;
    }

public function update($id, $data)
{
    $user = Auth::user();

    if ($user->role !== 'teacher') {
        abort(403, 'Hanya teacher yang bisa update');
    }

    $student = Student::findOrFail($id);
    $student->update($data);

    return $student;
}

public function delete($id)
{
    $user = Auth::user();

    if ($user->role !== 'teacher') {
        abort(403, 'Hanya teacher yang bisa delete');
    }

    $student = Student::findOrFail($id);
    $student->delete();
}
}
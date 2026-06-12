<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClassController extends Controller
{
    // ─── Helper: auto buat akun parent ───────────────────────────────────────

    private function createParentAccount(?string $name, ?string $phone, ?string $email, ?string $password): ?int
    {
        if (empty($name) || empty($email)) return null;

        $parent = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'parent',
            'phone'    => $phone,
        ]);

        return $parent->id;
    }

    // ─── Helper: upload foto ke Cloudinary ───────────────────────────────────

    private function uploadPhoto($file): string
    {
        $uploaded = cloudinary()->upload($file->getRealPath(), [
            'folder'         => 'guru-report/students',
            'resource_type'  => 'image',
            'transformation' => [
                'quality'      => 'auto',
                'fetch_format' => 'auto',
                'width'        => 800,
                'height'       => 800,
                'crop'         => 'fill',
                'gravity'      => 'face',
            ],
        ]);

        return $uploaded->getSecurePath();
    }

    // ─── Helper: hapus foto dari Cloudinary ──────────────────────────────────

    private function deletePhoto(?string $url): void
    {
        if (!$url) return;
        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches);
        if (!empty($matches[1])) {
            cloudinary()->destroy($matches[1]);
        }
    }

    // ─── GET /api/classes ─────────────────────────────────────────────────────

    public function index()
    {
        $classes = ClassRoom::with(['homeroomTeacher:id,name,role', 'students:id,name'])
            ->latest()
            ->get();

        return response()->json($classes);
    }

    // ─── GET /api/classes/{id} ────────────────────────────────────────────────

    public function show($id)
    {
        $class = ClassRoom::with(['homeroomTeacher:id,name,role', 'students:id,name'])
            ->findOrFail($id);

        return response()->json($class);
    }

    // ─── POST /api/classes ────────────────────────────────────────────────────

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

    // ─── PUT /api/classes/{id} ────────────────────────────────────────────────

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

    // ─── DELETE /api/classes/{id} ─────────────────────────────────────────────

    public function destroy($id)
    {
        $class = ClassRoom::findOrFail($id);
        $class->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }

    // ─── POST /api/classes/{id}/students ─────────────────────────────────────

    public function addStudent(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:100',
            'photo'           => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'birth_date'      => 'nullable|date',
            'gender'          => 'nullable|in:laki-laki,perempuan',
            'school_name'     => 'nullable|string|max:150',
            'address'         => 'nullable|string',
            'special_needs'   => 'nullable|in:autis,adhd,down_syndrome,lambat_belajar,tunarungu,tunawicara,tunagrahita,lainnya',
            'diagnosis_notes' => 'nullable|string',
            'father_name'     => 'nullable|string|max:100',
            'mother_name'     => 'nullable|string|max:100',
            'parent_phone'    => 'nullable|string|max:20',
            'parent_email'    => 'required|email|unique:users,email',
            'parent_password' => 'required|string|min:6',
        ]);

        // Upload foto ke Cloudinary
        $photoUrl = $request->hasFile('photo')
            ? $this->uploadPhoto($request->file('photo'))
            : null;

        // Auto buat akun parent
        $parentName = $request->father_name ?? $request->mother_name ?? null;
        $parentId   = $this->createParentAccount(
            $parentName,
            $request->parent_phone,
            $request->parent_email,
            $request->parent_password
        );

        $student = Student::create([
            'name'            => $request->name,
            'photo'           => $photoUrl,
            'birth_date'      => $request->birth_date,
            'gender'          => $request->gender,
            'school_name'     => $request->school_name,
            'address'         => $request->address,
            'special_needs'   => $request->special_needs,
            'diagnosis_notes' => $request->diagnosis_notes,
            'parent_id'       => $parentId,
            'parent_phone'    => $request->parent_phone,
            'father_name'     => $request->father_name,
            'mother_name'     => $request->mother_name,
        ]);

        $class->students()->attach($student->id);

        return response()->json([
            'success' => true,
            'message' => 'Murid berhasil ditambahkan.',
            'data'    => [
                'student' => $student->load('parent:id,name,email,role'),
                'parent_credentials' => [
                    'email'    => $request->parent_email,
                    'password' => $request->parent_password,
                ],
            ],
        ], 201);
    }

   // ─── PUT /api/classes/{id}/students/{studentId} ───────────────────────────

public function updateStudent(Request $request, $id, $studentId)
{
    dd($request->all());
    $class    = ClassRoom::findOrFail($id);
    $isMember = $class->students()->where('student_id', $studentId)->exists();

    if (!$isMember) {
        return response()->json([
            'message' => 'Murid tidak ditemukan di kelas ini.',
        ], 404);
    }

    $student = Student::findOrFail($studentId);

    $request->validate([
        'name'            => 'sometimes|string|max:100',
        'photo'           => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        'birth_date'      => 'nullable|date',
        'gender'          => 'nullable|in:laki-laki,perempuan',
        'school_name'     => 'nullable|string|max:150',
        'address'         => 'nullable|string',
        'special_needs'   => 'nullable|in:autis,adhd,down_syndrome,lambat_belajar,tunarungu,tunawicara,tunagrahita,lainnya',
        'diagnosis_notes' => 'nullable|string',
        'parent_phone'    => 'nullable|string|max:20',
        'father_name'     => 'nullable|string|max:100',
        'mother_name'     => 'nullable|string|max:100',
        'parent_email'    => 'nullable|email|unique:users,email,' . ($student->parent_id ?? 'NULL'),
        'parent_password' => 'nullable|string|min:6',
    ]);

$updateData = $request->only([
    'name',
    'birth_date',
    'gender',
    'school_name',
    'address',
    'special_needs',
    'diagnosis_notes',
    'parent_phone',
    'father_name',
    'mother_name',
]);

if ($request->birth_date) {
    $updateData['birth_date'] = \Carbon\Carbon::parse($request->birth_date)->toDateString();
}

    // Update foto jika ada
    if ($request->hasFile('photo')) {
        $this->deletePhoto($student->photo);
        $updateData['photo'] = $this->uploadPhoto($request->file('photo'));
    }

    // Update akun parent jika ada
if ($student->parent_id && ($request->parent_email || $request->parent_password)) {
    $parentUpdate = [];
    if ($request->parent_email) {
        $parentUpdate['email'] = $request->parent_email;
        // Update name juga jika father_name/mother_name berubah
        $parentUpdate['name'] = $request->father_name 
            ?? $request->mother_name 
            ?? $student->parent->name;
    }
    if ($request->parent_password) {
        $parentUpdate['password'] = Hash::make($request->parent_password);
    }
    User::where('id', $student->parent_id)->update($parentUpdate);
}

$student->update($updateData);

// ✅ Tambahkan refresh() agar relasi parent dimuat ulang dari DB
$student->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Data murid berhasil diupdate.',
        'data'    => [
            'student' => $student->load('parent:id,name,email,role'),
            'parent_credentials' => [
                'email'    => $request->parent_email ?? $student->parent?->email,
                'password' => $request->parent_password ? $request->parent_password : '(tidak diubah)',
            ],
        ],
    ]);
}

    // ─── DELETE /api/classes/{id}/students/{studentId} ────────────────────────

    public function removeStudent($id, $studentId)
    {
        $class   = ClassRoom::findOrFail($id);
        $student = Student::findOrFail($studentId);

        $this->deletePhoto($student->photo);
        $class->students()->detach($studentId);

        return response()->json([
            'success' => true,
            'message' => 'Murid berhasil dihapus dari kelas.',
        ]);
    }
}
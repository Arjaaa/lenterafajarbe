<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    const SPECIAL_NEEDS = [
        'autis', 'adhd', 'down_syndrome', 'lambat_belajar',
        'tunarungu', 'tunawicara', 'tunagrahita', 'lainnya',
    ];

    // ─── Upload foto ke Cloudinary ─────────────────────────────────────────────
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
                'gravity'      => 'face',   // auto crop ke wajah
            ],
        ]);

        return $uploaded->getSecurePath();
    }

    private function deletePhoto(?string $url): void
    {
        if (!$url) return;
        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches);
        if (!empty($matches[1])) {
            cloudinary()->destroy($matches[1]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    // GET /api/students
    public function index(Request $request)
    {
        $query = Student::with(['parent:id,name,email,phone'])
            ->latest();

        // Filter by special needs
        if ($request->has('special_needs')) {
            $query->where('special_needs', $request->special_needs);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->get());
    }

    // GET /api/students/{id}
    public function show($id)
    {
        $student = Student::with([
            'parent:id,name,email,phone',
            'classes:id,name',
            'shadowGroup',
            'oneOnOneGroup',
        ])->findOrFail($id);

        // Tambahkan umur
        $student->append('age');

        return response()->json($student);
    }

    // POST /api/students
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'photo'            => 'nullable|file|image|max:5120',
            'birth_date'       => 'nullable|date',
            'gender'           => 'nullable|in:laki-laki,perempuan',
            'school_name'      => 'nullable|string|max:150',
            'address'          => 'nullable|string',
            'special_needs'    => 'nullable|in:' . implode(',', self::SPECIAL_NEEDS),
            'diagnosis_notes'  => 'nullable|string',
            'parent_id'        => 'nullable|exists:users,id',
            'parent_phone'     => 'nullable|string|max:20',
        ]);

        // Validasi parent harus role parent
        if ($request->filled('parent_id')) {
            $parent = User::findOrFail($request->parent_id);
            if ($parent->role !== 'parent') {
                return response()->json([
                    'message' => 'User yang dipilih sebagai orang tua harus memiliki role parent.',
                ], 422);
            }
        }

        // Upload foto
        $photoUrl = $request->hasFile('photo')
            ? $this->uploadPhoto($request->file('photo'))
            : null;

        $student = Student::create([
            'name'            => $request->name,
            'photo'           => $photoUrl,
            'birth_date'      => $request->birth_date,
            'gender'          => $request->gender,
            'school_name'     => $request->school_name,
            'address'         => $request->address,
            'special_needs'   => $request->special_needs,
            'diagnosis_notes' => $request->diagnosis_notes,
            'parent_id'       => $request->parent_id,
            'parent_phone'    => $request->parent_phone,
        ]);

        return response()->json([
            'message' => 'Data murid berhasil ditambahkan.',
            'student' => $student->load('parent:id,name,email,phone'),
        ], 201);
    }

    // PUT /api/students/{id}
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name'            => 'sometimes|string|max:100',
            'photo'           => 'nullable|file|image|max:5120',
            'birth_date'      => 'nullable|date',
            'gender'          => 'nullable|in:laki-laki,perempuan',
            'school_name'     => 'nullable|string|max:150',
            'address'         => 'nullable|string',
            'special_needs'   => 'nullable|in:' . implode(',', self::SPECIAL_NEEDS),
            'diagnosis_notes' => 'nullable|string',
            'parent_id'       => 'nullable|exists:users,id',
            'parent_phone'    => 'nullable|string|max:20',
        ]);

        if ($request->filled('parent_id')) {
            $parent = User::findOrFail($request->parent_id);
            if ($parent->role !== 'parent') {
                return response()->json([
                    'message' => 'User yang dipilih sebagai orang tua harus memiliki role parent.',
                ], 422);
            }
        }

        $updateData = $request->only([
            'name', 'birth_date', 'gender', 'school_name',
            'address', 'special_needs', 'diagnosis_notes',
            'parent_id', 'parent_phone',
        ]);

        // Update foto jika ada
        if ($request->hasFile('photo')) {
            $this->deletePhoto($student->photo);
            $updateData['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $student->update($updateData);

        return response()->json([
            'message' => 'Data murid berhasil diupdate.',
            'student' => $student->load('parent:id,name,email,phone'),
        ]);
    }

    // DELETE /api/students/{id}
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $this->deletePhoto($student->photo);
        $student->delete();

        return response()->json(['message' => 'Data murid berhasil dihapus.']);
    }

    // GET /api/students/special-needs-options
    // Helper untuk frontend ambil pilihan kebutuhan khusus
    public function specialNeedsOptions()
    {
        return response()->json(['special_needs' => self::SPECIAL_NEEDS]);
    }
}
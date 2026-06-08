<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                'gravity'      => 'face',
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

        if ($request->has('special_needs')) {
            $query->where('special_needs', $request->special_needs);
        }

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

        $student->append('age');

        return response()->json($student);
    }

    // GET /api/students/{id}/dashboard
    public function dashboard($id)
    {
        $student = Student::findOrFail($id);
        $today = now()->toDateString();

        $todayReports = DailyReport::where('student_id', $id)
            ->where('date', $today)
            ->count();

        $recentReports = DailyReport::with(['detail', 'shadowTeacher:id,name', 'therapist:id,name'])
            ->where('student_id', $id)
            ->latest('date')
            ->take(3)
            ->get()
            ->map(function ($report) use ($student) {
                return [
                    'id'          => $report->id,
                    'date'        => $report->date,
                    'title'       => $report->detail->activity_notes
                                        ? Str::words($report->detail->activity_notes, 2, '')
                                        : 'Laporan Harian',
                    'description' => $student->name . ' ' . ($report->detail->activity_notes ?? '-'),
                    'created_by'  => $report->shadowTeacher->name ?? $report->therapist->name ?? '-',
                ];
            });

        return response()->json([
            'student' => [
                'id'        => $student->id,
                'name'      => $student->name,
                'photo'     => $student->photo,
                'address'   => $student->address,
                'is_active' => true,
            ],
            'today_reports'    => $todayReports,
            'today_activities' => $todayReports,
            'recent_reports'   => $recentReports,
        ]);
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
            'parent_phone'     => 'nullable|string|max:20',
            'father_name'      => 'nullable|string|max:100',
            'mother_name'      => 'nullable|string|max:100',
        ]);

        $parentName = $request->father_name ?? $request->mother_name ?? 'Orang Tua';
        $parent = User::create([
            'name'     => $parentName,
            'email'    => 'parent_' . time() . '@lenterafajar.id',
            'password' => bcrypt('password123'),
            'role'     => 'parent',
            'phone'    => $request->parent_phone,
        ]);

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
            'parent_id'       => $parent->id,
            'parent_phone'    => $request->parent_phone,
            'father_name'     => $request->father_name,
            'mother_name'     => $request->mother_name,
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
    public function specialNeedsOptions()
    {
        return response()->json(['special_needs' => self::SPECIAL_NEEDS]);
    }
   public function myStudents(Request $request)
{
    /** @var \App\Models\User $user */
    $user = $request->user();

    if ($user->isCoordinator()) {
        $students = Student::with('parent:id,name,email,phone')->get();

    } elseif ($user->isShadowTeacher()) {
        $studentIds = \App\Models\ShadowGroup::where('pic_id', $user->id)
            ->orWhere('partner_id', $user->id)
            ->pluck('student_id');
        $students = Student::with('parent:id,name,email,phone')
            ->whereIn('id', $studentIds)
            ->get();

    } elseif ($user->role === 'therapist_homeroom') {
        // Wali kelas — ambil siswa dari kelas yang dia pegang
        $studentIds = \App\Models\ClassRoom::where('homeroom_teacher_id', $user->id)
            ->with('students:id')
            ->get()
            ->pluck('students')
            ->flatten()
            ->pluck('id');
        $students = Student::with('parent:id,name,email,phone')
            ->whereIn('id', $studentIds)
            ->get();

    } elseif ($user->role === 'therapist') {
        $studentIds = \App\Models\OneOnOneGroup::where('teacher_id', $user->id)
            ->pluck('student_id');
        $students = Student::with('parent:id,name,email,phone')
            ->whereIn('id', $studentIds)
            ->get();

    } else {
        return response()->json([
            'message' => 'Anda tidak memiliki akses ke data siswa.',
        ], 403);
    }

    return response()->json([
        'success' => true,
        'data'    => $students,
    ]);
}
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentProfileController extends Controller
{
    // GET /api/students/{studentId}/profile
public function show($studentId)
{
    $student = Student::with(['parent:id,name,email,phone', 'classes:id,name'])
        ->findOrFail($studentId);

    $age = $student->birth_date
        ? Carbon::parse($student->birth_date)->age . ' Tahun'
        : null;

    $gender = match ($student->gender) {
        'laki-laki' => 'Laki - Laki',
        'perempuan' => 'Perempuan',
        default     => null,
    };

    $today = now()->toDateString();
    $todayReports = \App\Models\DailyReport::where('student_id', $studentId)
        ->where('date', $today)
        ->count();

    return response()->json([
        'success' => true,
        'message' => 'Profile siswa berhasil diambil.',
        'data'    => [
            'header' => [
                'photo'            => $student->photo ?: null,
                'name'             => $student->name,
                'address'          => $student->address,
                'is_active'        => true,
                'today_reports'    => $todayReports,
                'today_activities' => $todayReports,
                'last_updated'     => 'Last updated : ' . $student->updated_at->format('d/m/Y'),
            ],
            'child_data' => [
                'age'     => $age,
                'gender'  => $gender,
                'address' => $student->address,
            ],
            'parent_data' => [
                'father_name' => $student->father_name,
                'mother_name' => $student->mother_name,
                'phone'       => $student->parent_phone ?? $student->parent?->phone,
                'email'       => $student->parent?->email,
            ],
            'diagnosis' => $student->diagnosis_notes,
            'class'     => $student->classes?->first()?->name,
        ],
    ]);
}
// GET /api/my-students
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
    } elseif ($user->isTherapist()) {
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
    // PUT /api/students/{studentId}/profile
    public function update(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $request->validate([
            'name'            => 'sometimes|string|max:100',
            'photo'           => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'birth_date'      => 'nullable|date',
            'gender'          => 'nullable|in:laki-laki,perempuan',
            'address'         => 'nullable|string',
            'school_name'     => 'nullable|string|max:150',
            'special_needs'   => 'nullable|in:autis,adhd,down_syndrome,lambat_belajar,tunarungu,tunawicara,tunagrahita,lainnya',
            'diagnosis_notes' => 'nullable|string',
            'father_name'     => 'nullable|string|max:100',
            'mother_name'     => 'nullable|string|max:100',
            'parent_phone'    => 'nullable|string|max:20',
            'parent_id'       => 'nullable|exists:users,id',
        ]);

        $updateData = $request->only([
            'name', 'birth_date', 'gender', 'address', 'school_name',
            'special_needs', 'diagnosis_notes', 'father_name',
            'mother_name', 'parent_phone', 'parent_id',
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($student->photo) {
                preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $student->photo, $matches);
                if (!empty($matches[1])) {
                    cloudinary()->destroy($matches[1]);
                }
            }

            $uploaded = cloudinary()->upload($request->file('photo')->getRealPath(), [
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

            $updateData['photo'] = $uploaded->getSecurePath();
        }

        $student->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profile siswa berhasil diupdate.',
            'data'    => [
                'header' => [
                    'photo'        => $student->photo ?: null,
                    'name'         => $student->name,
                    'last_updated' => 'Last updated : ' . $student->updated_at->format('d/m/Y'),
                ],
                'child_data' => [
                    'age'     => $student->birth_date
                        ? \Carbon\Carbon::parse($student->birth_date)->age . ' Tahun'
                        : null,
                    'gender'  => match ($student->gender) {
                        'laki-laki' => 'Laki - Laki',
                        'perempuan' => 'Perempuan',
                        default     => null,
                    },
                    'address' => $student->address,
                ],
                'parent_data' => [
                    'father_name' => $student->father_name,
                    'mother_name' => $student->mother_name,
                    'phone'       => $student->parent_phone ?? $student->parent?->phone,
                    'email'       => $student->parent?->email,
                ],
                'diagnosis' => $student->diagnosis_notes,
                'class'     => $student->classes()->first()?->name,
            ],
        ]);
    }
}
<?php
namespace App\Http\Controllers\Koor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Student;

class KelasController extends Controller
{
    public function dataKelas()
    {
        $classes = ClassRoom::with(['homeroomTeacher', 'homeroomTeacher2'])->withCount('students')->get();
        $teachers = User::where('role', 'therapist_homeroom')->get();
        $assignedTeacherIds = ClassRoom::pluck('homeroom_teacher_id')
            ->merge(ClassRoom::pluck('homeroom_teacher_2_id'))
            ->filter()
            ->toArray();

        return view('admin.data-kelas', compact('classes', 'teachers', 'assignedTeacherIds'));
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'homeroom_teacher_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (ClassRoom::where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value)->exists()) {
                        $fail('Guru ini sudah menjadi Wali Kelas di ruangan lain.');
                    }
                }
            ],
            'homeroom_teacher_2_id' => [
                'nullable',
                'exists:users,id',
                'different:homeroom_teacher_id',
                function ($attribute, $value, $fail) {
                    if (ClassRoom::where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value)->exists()) {
                        $fail('Guru ini sudah menjadi Wali Kelas di ruangan lain.');
                    }
                }
            ],
        ]);

        ClassRoom::create($request->all());
        return redirect()->route('koor.dataKelas')->with('success', 'Data Kelas berhasil ditambahkan!');
    }

    public function updateKelas(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'homeroom_teacher_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($id) {
                    if (
                        ClassRoom::where('id', '!=', $id)->where(function ($q) use ($value) {
                            $q->where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value);
                        })->exists()
                    ) {
                        $fail('Guru Utama sudah menjadi Wali Kelas di ruangan lain.');
                    }
                }
            ],
            'homeroom_teacher_2_id' => [
                'nullable',
                'exists:users,id',
                'different:homeroom_teacher_id',
                function ($attribute, $value, $fail) use ($id) {
                    if (
                        ClassRoom::where('id', '!=', $id)->where(function ($q) use ($value) {
                            $q->where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value);
                        })->exists()
                    ) {
                        $fail('Guru Pendamping sudah menjadi Wali Kelas di ruangan lain.');
                    }
                }
            ],
        ]);

        $class->update($request->all());
        return redirect()->back()->with('success', 'Data Kelas berhasil diupdate!');
    }

    public function destroyKelas($id)
    {
        ClassRoom::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Kelas berhasil dihapus!');
    }

    public function detailKelas($id)
    {
        $class = ClassRoom::with(['homeroomTeacher', 'homeroomTeacher2', 'students'])->findOrFail($id);
        $allStudents = Student::all();
        return view('admin.detail-kelas', compact('class', 'allStudents'));
    }

    public function tambahMuridKeKelas(Request $request, $classId)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);
        $class = ClassRoom::findOrFail($classId);
        $class->students()->syncWithoutDetaching($request->student_ids);
        return redirect()->back()->with('success', 'Semua murid yang dicentang berhasil dimasukkan ke kelas!');
    }

    public function keluarkanMuridDariKelas($classId, $studentId)
    {
        $class = ClassRoom::findOrFail($classId);
        $class->students()->detach($studentId);
        return redirect()->back()->with('success', 'Murid berhasil dikeluarkan dari kelas.');
    }
}
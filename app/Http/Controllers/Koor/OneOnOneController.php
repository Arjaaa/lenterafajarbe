<?php
namespace App\Http\Controllers\Koor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OneOnOneGroup;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\ShadowGroup;
use Illuminate\Support\Facades\DB;

class OneOnOneController extends Controller
{
    public function data1on1()
    {
        $oneOnOnes = OneOnOneGroup::with(['student', 'teacher'])->get();
        $students = Student::all();
        $teachers = User::whereIn('role', ['therapist', 'therapist_homeroom'])->get();

        $studentInClass = DB::table('class_students')->pluck('student_id')->filter()->toArray();
        $studentInShadow = ShadowGroup::pluck('student_id')->filter()->toArray();
        $studentIn1on1 = OneOnOneGroup::pluck('student_id')->filter()->toArray();
        $busyStudentIds = array_unique(array_merge($studentInClass, $studentInShadow, $studentIn1on1));

        $teacherInClass1 = ClassRoom::pluck('homeroom_teacher_id')->filter()->toArray();
        $teacherInClass2 = ClassRoom::pluck('homeroom_teacher_2_id')->filter()->toArray();
        $teacherInShadowPic = ShadowGroup::pluck('pic_id')->filter()->toArray();
        $teacherInShadowPartner = ShadowGroup::pluck('partner_id')->filter()->toArray();
        $teacherIn1on1 = OneOnOneGroup::pluck('teacher_id')->filter()->toArray();

        $busyTeacherIds = array_unique(array_merge($teacherInClass1, $teacherInClass2, $teacherInShadowPic, $teacherInShadowPartner, $teacherIn1on1));

        return view('admin.data-1on1', compact('oneOnOnes', 'students', 'teachers', 'busyStudentIds', 'busyTeacherIds'));
    }

    public function store1on1(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => [
                'required',
                'exists:students,id',
                function ($attribute, $value, $fail) {
                    if (OneOnOneGroup::where('student_id', $value)->exists() || DB::table('class_students')->where('student_id', $value)->exists() || ShadowGroup::where('student_id', $value)->exists()) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum, Shadow, atau 1on1 lain.');
                    }
                }
            ],
            'teacher_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (OneOnOneGroup::where('teacher_id', $value)->exists() || ClassRoom::where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value)->exists() || ShadowGroup::where('pic_id', $value)->orWhere('partner_id', $value)->exists()) {
                        $fail('Terapis ini sudah memegang Kelas atau Group lain.');
                    }
                }
            ],
        ]);

        OneOnOneGroup::create($request->all());
        return redirect()->route('koor.data1on1')->with('success', 'Sesi Terapi 1 on 1 berhasil dibuat!');
    }

    public function update1on1(Request $request, $id)
    {
        $oneOnOne = OneOnOneGroup::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => [
                'required',
                'exists:students,id',
                function ($attribute, $value, $fail) use ($oneOnOne) {
                    if ($value != $oneOnOne->student_id && (OneOnOneGroup::where('student_id', $value)->exists() || DB::table('class_students')->where('student_id', $value)->exists() || ShadowGroup::where('student_id', $value)->exists())) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum, Shadow, atau 1on1 lain.');
                    }
                }
            ],
            'teacher_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($oneOnOne) {
                    if ($value != $oneOnOne->teacher_id && (OneOnOneGroup::where('teacher_id', $value)->exists() || ClassRoom::where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value)->exists() || ShadowGroup::where('pic_id', $value)->orWhere('partner_id', $value)->exists())) {
                        $fail('Terapis ini sudah memegang Kelas atau Group lain.');
                    }
                }
            ],
        ]);

        $oneOnOne->update($request->all());
        return redirect()->back()->with('success', 'Sesi Terapi 1 on 1 berhasil diupdate!');
    }

    public function destroy1on1($id)
    {
        OneOnOneGroup::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Sesi Terapi 1 on 1 berhasil dihapus!');
    }

    public function show($id)
    {
        // Mengambil data sesi 1on1 beserta relasi anak dan terapisnya
        $oneOnOne = \App\Models\OneOnOneGroup::with(['student', 'teacher'])->findOrFail($id);

        return view('admin.detail-1on1', compact('oneOnOne'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\StoreParentRequest;
use App\Http\Requests\StoreGuruRequest;
use App\Models\ShadowGroup;
use App\Models\OneOnOneGroup;
use Illuminate\Support\Facades\Hash;
use App\Models\ClassRoom;
class KoorController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function dataAnak()
    {
        $students = Student::all();
        $parents = User::where('role', 'parent')->get();

        return view('admin.data-anak', compact('students', 'parents'));

    }
    public function storeAnak(StoreStudentRequest $request)
    {
        Student::create($request->validated());

        return redirect()->route('koor.dataAnak')->with('success', 'Data anak berhasil ditambahkan!');
    }
    public function dataOrangTua()
    {
        $parents = User::where('role', 'parent')->get();
        return view('admin.data-orang-tua', compact('parents'));
    }

    public function storeOrangTua(StoreParentRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make('lentera123');

        $data['role'] = 'parent';

        User::create($data);

        return redirect()->route('koor.dataOrangTua')->with('success', 'Data Orang Tua berhasil dicatat!');
    }
    public function dataGuru()
    {
        $gurus = User::whereIn('role', [
            'shadow_pj',
            'shadow_teacher',
            'therapist_homeroom',
            'therapist'
        ])->get();

        return view('admin.data-guru', compact('gurus'));
    }

    public function storeGuru(StoreGuruRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make('lentera123');

        User::create($data);

        return redirect()->route('koor.dataGuru')->with('success', 'Data Guru/Terapis berhasil ditambahkan!');
    }
    public function updateOrangTua(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));
        return redirect()->back()->with('success', 'Data Orang Tua berhasil diupdate!');
    }

    public function destroyOrangTua($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Orang Tua berhasil dihapus!');
    }

    public function updateGuru(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:shadow_pj,shadow_teacher,therapist_homeroom,therapist',
            'is_active' => 'required|boolean',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'role', 'is_active']));

        return redirect()->back()->with('success', 'Data Guru/Terapis berhasil diupdate!');
    }

    public function destroyGuru($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Guru/Terapis berhasil dihapus!');
    }

    public function updateAnak(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'school_name' => 'nullable|string|max:255',
            'special_needs' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        $student->update($request->all());
        return redirect()->back()->with('success', 'Data Anak berhasil diupdate!');
    }

    public function destroyAnak($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Anak berhasil dihapus!');
    }
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
                    // Cek apakah guru ini udah jadi wali 1 atau wali 2 di kelas lain
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
                    // Cek guru di kelas lain (TAPI abaikan kelas yang lagi di-edit ini)
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
        $allStudents = \App\Models\Student::all();

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
        $class->students()->detach($studentId); // Memutuskan hubungan di tabel pivot
        return redirect()->back()->with('success', 'Murid berhasil dikeluarkan dari kelas.');
    }
    public function dataShadowGroup()
    {
        $shadowGroups = ShadowGroup::with(['student', 'pic', 'partner'])->get();

        $students = \App\Models\Student::all();
        $pjs = User::where('role', 'shadow_pj')->get();
        $partners = User::where('role', 'shadow_teacher')->get();

        // 1. Cari Anak yang sibuk
        $studentInShadow = ShadowGroup::pluck('student_id')->filter()->toArray();
        $studentInClass = \DB::table('class_students')->pluck('student_id')->filter()->toArray();
        $busyStudentIds = array_unique(array_merge($studentInShadow, $studentInClass));

        // 2. Cari Guru Shadow (Partner) yang sibuk
        $assignedPartnerIds = ShadowGroup::pluck('partner_id')->filter()->toArray();

        // 3. Cari PJ Shadow yang sudah pegang grup (BARU 💡)
        $assignedPicIds = ShadowGroup::pluck('pic_id')->filter()->toArray();

        // Kirim semua filternya ke view
        return view('admin.data-shadow', compact('shadowGroups', 'students', 'pjs', 'partners', 'busyStudentIds', 'assignedPartnerIds', 'assignedPicIds'));
    }

    public function storeShadowGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => [
                'required',
                'exists:students,id',
                function ($attribute, $value, $fail) {
                    if (ShadowGroup::where('student_id', $value)->exists() || \DB::table('class_students')->where('student_id', $value)->exists()) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum atau Group lain.');
                    }
                }
            ],
            'pic_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    // Cek apakah PJ ini sudah ada di grup lain (BARU 💡)
                    if (ShadowGroup::where('pic_id', $value)->exists()) {
                        $fail('PJ Shadow ini sudah memegang Group lain.');
                    }
                }
            ],
            'partner_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (ShadowGroup::where('partner_id', $value)->exists()) {
                        $fail('Guru Shadow ini sudah ditugaskan mendampingi anak lain.');
                    }
                }
            ],
            'school_name' => 'required|string|max:255',
        ]);

        ShadowGroup::create($request->all());
        return redirect()->route('koor.dataShadowGroup')->with('success', 'Group Shadow Teacher berhasil dibuat!');
    }

    public function updateShadowGroup(Request $request, $id)
    {
        $group = ShadowGroup::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => [
                'required',
                'exists:students,id',
                function ($attribute, $value, $fail) use ($group) {
                    if ($value != $group->student_id && (ShadowGroup::where('student_id', $value)->exists() || \DB::table('class_students')->where('student_id', $value)->exists())) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum atau Group lain.');
                    }
                }
            ],
            'pic_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($group) {
                    // Cek apakah PJ ini sudah ada di grup lain, kecuali grup ini sendiri (BARU 💡)
                    if ($value != $group->pic_id && ShadowGroup::where('pic_id', $value)->exists()) {
                        $fail('PJ Shadow ini sudah memegang Group lain.');
                    }
                }
            ],
            'partner_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($group) {
                    if ($value != $group->partner_id && ShadowGroup::where('partner_id', $value)->exists()) {
                        $fail('Guru Shadow ini sudah ditugaskan mendampingi anak lain.');
                    }
                }
            ],
            'school_name' => 'required|string|max:255',
        ]);

        $group->update($request->all());
        return redirect()->back()->with('success', 'Group Shadow Teacher berhasil diupdate!');
    }

    public function destroyShadowGroup($id)
    {
        ShadowGroup::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Group Shadow Teacher berhasil dihapus!');
    }

    // ==========================================
    // FUNGSI CRUD KELAS 1 ON 1
    // ==========================================
    public function data1on1()
    {
        $oneOnOnes = OneOnOneGroup::with(['student', 'teacher'])->get();
        $students = \App\Models\Student::all();
        $teachers = User::whereIn('role', ['therapist', 'therapist_homeroom'])->get();

        // 1. FILTER ANAK SIBUK (Cek di Kelas Umum, Shadow, dan 1on1)
        $studentInClass = \DB::table('class_students')->pluck('student_id')->filter()->toArray();
        $studentInShadow = \App\Models\ShadowGroup::pluck('student_id')->filter()->toArray();
        $studentIn1on1 = OneOnOneGroup::pluck('student_id')->filter()->toArray();
        $busyStudentIds = array_unique(array_merge($studentInClass, $studentInShadow, $studentIn1on1));

        // 2. FILTER GURU SIBUK (Cek di Kelas Umum, Shadow, dan 1on1)
        $teacherInClass1 = \App\Models\ClassRoom::pluck('homeroom_teacher_id')->filter()->toArray();
        $teacherInClass2 = \App\Models\ClassRoom::pluck('homeroom_teacher_2_id')->filter()->toArray();
        $teacherInShadowPic = \App\Models\ShadowGroup::pluck('pic_id')->filter()->toArray();
        $teacherInShadowPartner = \App\Models\ShadowGroup::pluck('partner_id')->filter()->toArray();
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
                    if (OneOnOneGroup::where('student_id', $value)->exists() || \DB::table('class_students')->where('student_id', $value)->exists() || \App\Models\ShadowGroup::where('student_id', $value)->exists()) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum, Shadow, atau 1on1 lain.');
                    }
                }
            ],
            'teacher_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (OneOnOneGroup::where('teacher_id', $value)->exists() || \App\Models\ClassRoom::where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value)->exists() || \App\Models\ShadowGroup::where('pic_id', $value)->orWhere('partner_id', $value)->exists()) {
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
                    if ($value != $oneOnOne->student_id && (OneOnOneGroup::where('student_id', $value)->exists() || \DB::table('class_students')->where('student_id', $value)->exists() || \App\Models\ShadowGroup::where('student_id', $value)->exists())) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum, Shadow, atau 1on1 lain.');
                    }
                }
            ],
            'teacher_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($oneOnOne) {
                    if ($value != $oneOnOne->teacher_id && (OneOnOneGroup::where('teacher_id', $value)->exists() || \App\Models\ClassRoom::where('homeroom_teacher_id', $value)->orWhere('homeroom_teacher_2_id', $value)->exists() || \App\Models\ShadowGroup::where('pic_id', $value)->orWhere('partner_id', $value)->exists())) {
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
}
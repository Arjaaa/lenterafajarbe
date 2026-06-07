<?php
namespace App\Http\Controllers\Koor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShadowGroup;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ShadowController extends Controller
{
    public function dataShadowGroup()
    {
        $shadowGroups = ShadowGroup::with(['student', 'pic', 'partner'])->get();
        $students = Student::all();
        $pjs = User::where('role', 'shadow_pj')->get();
        $partners = User::where('role', 'shadow_teacher')->get();

        $studentInShadow = ShadowGroup::pluck('student_id')->filter()->toArray();
        $studentInClass = DB::table('class_students')->pluck('student_id')->filter()->toArray();
        $busyStudentIds = array_unique(array_merge($studentInShadow, $studentInClass));

        $assignedPartnerIds = ShadowGroup::pluck('partner_id')->filter()->toArray();
        $assignedPicIds = ShadowGroup::pluck('pic_id')->filter()->toArray();

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
                    if (ShadowGroup::where('student_id', $value)->exists() || DB::table('class_students')->where('student_id', $value)->exists()) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum atau Group lain.');
                    }
                }
            ],
            'pic_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
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
                    if ($value != $group->student_id && (ShadowGroup::where('student_id', $value)->exists() || DB::table('class_students')->where('student_id', $value)->exists())) {
                        $fail('Anak ini sudah terdaftar di Kelas Umum atau Group lain.');
                    }
                }
            ],
            'pic_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($group) {
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
    public function show($id)
{
    // Mengambil data group shadow beserta relasi anak, pj, dan partnernya
    $shadow = \App\Models\ShadowGroup::with(['student', 'pic', 'partner'])->findOrFail($id);
    
    return view('admin.detail-shadow', compact('shadow'));
}
}
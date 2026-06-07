<?php
namespace App\Http\Controllers\Koor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreStudentRequest;

class AnakController extends Controller
{
    public function dataAnak()
    {
        $students = Student::all();
        $parents = User::where('role', 'parent')->get();
        return view('admin.data-anak', compact('students', 'parents'));
    }

    public function storeAnak(Request $request)
    {
        // 1. Jalankan Validasi Bersyarat Tergantung Tab Mana Yang Aktif
        if ($request->ortu_status == 'lama') {
            $request->validate([
                'name_lama' => 'required|string|max:255',
                'parent_id' => 'required|exists:users,id',
                'photo_lama' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $dataAnak = [
                'name' => $request->name_lama,
                'birth_date' => $request->birth_date_lama,
                'gender' => $request->gender_lama,
                'school_name' => $request->school_name_lama,
                'special_needs' => $request->special_needs_lama,
                'diagnosis_notes' => $request->diagnosis_notes_lama,
                'address' => $request->address_lama,
                'parent_id' => $request->parent_id,
            ];

            if ($request->hasFile('photo_lama')) {
                $dataAnak['photo'] = $request->file('photo_lama')->store('students_photos', 'public');
            }

        } else {
            // Validasi jika tab Ortu Baru diisi
            $request->validate([
                'name_baru' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'parent_phone' => 'required|string|max:20',
                'parent_email' => 'required|email|unique:users,email',
                'parent_password' => 'required|string|min:6', // Validasi password inputan
                'photo_baru' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Buat User Baru untuk akun Ortu menggunakan password yang diinput
            $newParent = \App\Models\User::create([
                'name' => $request->mother_name, // Otomatis menggunakan nama ibu sebagai display name akun
                'email' => $request->parent_email,
                'phone' => $request->parent_phone,
                'password' => Hash::make($request->parent_password), // Menggunakan password dari form
                'role' => 'parent',
            ]);

            // Mapping data dari tab Ortu Baru (Full Form)
            $dataAnak = [
                'name' => $request->name_baru,
                'birth_date' => $request->birth_date_baru,
                'gender' => $request->gender_baru,
                'school_name' => $request->school_name_baru,
                'special_needs' => $request->special_needs_baru,
                'diagnosis_notes' => $request->diagnosis_notes_baru,
                'address' => $request->address_baru,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'parent_id' => $newParent->id,
            ];

            if ($request->hasFile('photo_baru')) {
                $dataAnak['photo'] = $request->file('photo_baru')->store('students_photos', 'public');
            }
        }

        // 2. Simpan Data ke Database
        Student::create($dataAnak);

        return redirect()->route('koor.dataAnak')->with('success', 'Data anak dan informasi orang tua berhasil diproses!');
    }

    public function updateAnak(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
        ]);

        $dataAnak = $request->all();

        // Update foto jika ada gambar baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $dataAnak['photo'] = $request->file('photo')->store('students_photos', 'public');
        }

        $student->update($dataAnak);
        return redirect()->back()->with('success', 'Data Anak berhasil diupdate!');
    }

    public function destroyAnak($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Anak berhasil dihapus!');
    }
}
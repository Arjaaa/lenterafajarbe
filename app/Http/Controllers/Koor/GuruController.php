<?php
namespace App\Http\Controllers\Koor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreGuruRequest;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
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
}
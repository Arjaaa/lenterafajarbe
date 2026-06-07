<?php
namespace App\Http\Controllers\Koor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreParentRequest;
use Illuminate\Support\Facades\Hash;

class OrangTuaController extends Controller
{
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
}
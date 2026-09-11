<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreParentRequest;
use Illuminate\Support\Facades\Http;

class OrangTuaController extends Controller
{
    public function dataOrangTua()
    {
        // 1. Ambil data Orang Tua dari API Arza menggunakan endpoint /users dan parameter role
        $apiUrl = env('API_BASE_URL', 'http://202.155.13.212/api') . '/users';
        $apiToken = session('api_token');

        $response = Http::withToken($apiToken)->get($apiUrl, [
            'role' => 'parent'
        ]);

        $parents = [];

        if ($response->successful()) {
            $apiData = $response->object();
            $parents = $apiData->data ?? [];
        }

        // Proteksi tipe data
        if (is_bool($parents) || !is_iterable($parents)) {
            $parents = [];
        }

        return view('admin.data-orang-tua', compact('parents'));
    }

    public function storeOrangTua(StoreParentRequest $request)
    {
        // Gunakan endpoint /users untuk Create
        $apiUrl = env('API_BASE_URL', 'http://202.155.13.212/api') . '/users';
        $apiToken = session('api_token');

        $data = $request->validated();

        $response = Http::withToken($apiToken)->post($apiUrl, [
            'name' => $data['name'] ?? $request->name,
            'email' => $data['email'] ?? $request->email,
            'phone' => $data['phone'] ?? $request->phone,
            'role' => 'parent', // <-- Wajib disisipkan agar Arza tahu ini akun Ortu
        ]);

        if ($response->successful()) {
            return redirect()->route('koor.dataOrangTua')->with('success', 'Data Orang Tua berhasil dicatat via API!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal menyimpan data ke server VPS.';
        return redirect()->back()->with('error', 'Gagal mencatat Orang Tua: ' . $errorMsg)->withInput();
    }

    public function updateOrangTua(Request $request, $id)
    {
        // Gunakan endpoint /users/{id} untuk Update
        $apiUrl = env('API_BASE_URL', 'http://202.155.13.212/api') . '/users/' . $id;
        $apiToken = session('api_token');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $response = Http::withToken($apiToken)->put($apiUrl, [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'parent', // Pastikan rolenya tidak berubah
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data Orang Tua berhasil diupdate!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal memperbarui data di server VPS.';
        return redirect()->back()->with('error', 'Gagal update: ' . $errorMsg);
    }

    public function destroyOrangTua($id)
    {
        // Gunakan endpoint /users/{id} untuk Delete
        $apiUrl = env('API_BASE_URL', 'http://202.155.13.212/api') . '/users/' . $id;
        $apiToken = session('api_token');

        $response = Http::withToken($apiToken)->delete($apiUrl);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data Orang Tua berhasil dihapus!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal menghapus data di server VPS.';
        return redirect()->back()->with('error', 'Gagal menghapus: ' . $errorMsg);
    }
}
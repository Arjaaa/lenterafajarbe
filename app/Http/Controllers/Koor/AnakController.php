<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnakController extends Controller
{
    // ==========================================
    // 1. TAMPILKAN DATA ANAK (Limit 10 per page)
    // ==========================================
    public function dataAnak(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Tangkap parameter dari URL
        $page = (int) $request->query('page', 1);
        $searchQuery = $request->query('search'); // <-- TANGKAP INPUTAN PENCARIAN
        $perPage = 10;

        // 2. Tembak API (Kita tarik semua datanya dulu buat di-filter)
        $studentRes = Http::withToken($apiToken)->get($baseUrl . '/students');

        $students = [];
        $pagination = ['current_page' => 1, 'last_page' => 1];

        if ($studentRes->successful()) {
            $apiData = $studentRes->json();
            $rawData = $apiData['data'] ?? $apiData ?? [];

            // Ubah data mentah jadi Laravel Collection
            $collection = collect($rawData);

            // ==========================================
            // LOGIKA PENCARIAN (Berdasarkan Nama Anak / Ortu)
            // ==========================================
            if (!empty($searchQuery)) {
                $collection = $collection->filter(function ($item) use ($searchQuery) {
                    $namaSiswa = $item['name'] ?? '';
                    $namaOrtu = $item['parent']['name'] ?? $item['mother_name'] ?? '';

                    // Cek apakah inputan user ada di nama siswa ATAU nama ortunya
                    return stripos($namaSiswa, $searchQuery) !== false ||
                        stripos($namaOrtu, $searchQuery) !== false;
                });
            }

            // ==========================================
            // PAGINATION MANUAL (Dari data yang terfilter)
            // ==========================================
            $totalData = $collection->count();
            $lastPage = (int) ceil($totalData / $perPage);

            // Jaga-jaga kalau pas search halamannya kosong, balik ke page 1
            $page = $page > $lastPage ? max($lastPage, 1) : $page;

            $students = json_decode(json_encode(
                $collection->forPage($page, $perPage)->values()->all()
            ));

            $pagination = [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
            ];
        }

        // 3. Ambil Data Orang Tua dari API (Untuk dropdown di Modal Tambah)
        $parentRes = Http::withToken($apiToken)->get($baseUrl . '/users', [
            'role' => 'parent'
        ]);

        $parents = [];
        if ($parentRes->successful()) {
            $parentData = $parentRes->json();
            $parents = json_decode(json_encode($parentData['data'] ?? []));
        }

        // 4. Proteksi iterasi Blade
        if (!is_iterable($students))
            $students = [];
        if (!is_iterable($parents))
            $parents = [];

        return view('admin.data-anak', compact('students', 'parents', 'pagination'));
    }

    // ==========================================
    // 2. SIMPAN DATA ANAK KE API
    // ==========================================
    public function storeAnak(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'required|email',
            'parent_password' => 'required|string|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Siapkan Client & Payload
        $httpClient = Http::withToken($apiToken);

        $payload = [
            'name' => $request->name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'school_name' => $request->school_name,
            'special_needs' => $request->special_needs,
            'diagnosis_notes' => $request->diagnosis_notes,
            'address' => $request->address,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'parent_phone' => $request->parent_phone,
            'parent_email' => $request->parent_email,
            'parent_password' => $request->parent_password,
        ];

        // 3. Lampirkan Foto jika ada
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $httpClient = $httpClient->attach(
                'photo',
                file_get_contents($file),
                $file->getClientOriginalName()
            );
        }

        // 4. Kirim ke API (Backend Mas Arza yang handle pendaftaran double ini)
        $response = $httpClient->post($baseUrl . '/students', $payload);

        if ($response->successful()) {
            return redirect()->route('koor.dataAnak')->with('success', 'Pendaftaran Akun Ortu & Siswa Berhasil!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal daftar ke API Server.';
        return redirect()->back()->withErrors(['error' => $errorMsg])->withInput();
    }

    // ==========================================
    // 3. UPDATE DATA ANAK KE API
    // ==========================================
    // ==========================================
    // 3. UPDATE DATA ANAK KE API
    // ==========================================
    public function updateAnak(Request $request, $id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Validasi Input Data
        $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_password' => 'nullable|string|min:6', // Validasi nullable (opsional) minimal 6
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Siapkan HTTP Client dengan Token
        $httpClient = Http::withToken($apiToken);

        // 3. Ambil payload
        $payload = $request->except(['_token', '_method', 'photo']);

        // JIKA FIELD PASSWORD KOSONG, HAPUS DARI PAYLOAD (agar password lama tidak berubah)
        if (empty($payload['parent_password'])) {
            unset($payload['parent_password']);
        }

        // Trik form-data API: Pakai POST tapi paksa method-nya jadi PUT
        $payload['_method'] = 'PUT';

        // 4. Jika user mengupload foto baru, lampirkan ke API
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $httpClient = $httpClient->attach(
                'photo',
                file_get_contents($file),
                $file->getClientOriginalName()
            );
        }

        // 5. Tembak ke API Endpoint Update
        $response = $httpClient->post($baseUrl . '/students/' . $id, $payload);

        if ($response->successful()) {
            $resData = $response->json();

            // 6. Tangkap parent_credentials jika API mengembalikannya
            if (isset($resData['parent_credentials'])) {
                // Simpan ke session flash agar bisa dimunculkan di modal / sweetalert setelah halaman reload
                session()->flash('parent_credentials', $resData['parent_credentials']);
            }

            return redirect()->back()->with('success', 'Data anak & orang tua berhasil diperbarui!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal memperbarui data di server.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }

    // ==========================================
    // 4. HAPUS DATA ANAK VIA API
    // ==========================================
    public function destroyAnak($id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $response = Http::withToken($apiToken)->delete($baseUrl . '/students/' . $id);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data Anak berhasil dihapus!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal menghapus data di server.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }
    // ==========================================
    // 5. DETAIL DATA ANAK VIA API
    // ==========================================
    public function showAnak(Request $request, $id) // <-- Tambahkan Request $request di sini
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // Tangkap halaman asal dari query string, default ke 1 jika tidak ada
        $backPage = $request->query('back_page', 1);

        // Nembak API ambil detail 1 siswa
        $response = Http::withToken($apiToken)->get($baseUrl . '/students/' . $id);

        if ($response->successful()) {
            $apiData = $response->json();
            $rawData = $apiData['data'] ?? $apiData;
            $student = json_decode(json_encode($rawData));

            // Kirim variabel backPage ke Blade
            return view('admin.detail-anak', compact('student', 'backPage'));
        }

        return redirect()->route('koor.dataAnak')->withErrors(['error' => 'Data siswa tidak ditemukan di server.']);
    }
}
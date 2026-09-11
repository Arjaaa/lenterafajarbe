<?php
namespace App\Http\Controllers\Koor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreGuruRequest;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    // ==========================================
    // TAMBAH DATA GURU BARU VIA API
    // ==========================================
    public function storeGuru(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        // 1. CEK PAYLOAD POSTMAN
        // Coba inget-inget, pas nambahin dari BE (Postman), ada parameter lain nggak yang dikirim? 
        // Misalnya butuh ngirim role kosong atau penanda kalau dia guru?
        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,

            // CONTOH: Mungkin BE butuh parameter role dikirim null atau string kosong biar masuk kategori 'Belum Ditugaskan'
            // 'role' => null, 
        ];

        // 2. KITA DEBUG RESPONSE-NYA
        $response = Http::withToken($apiToken)->asForm()->post($baseUrl . '/register', $payload);

        // KODE SAKTI BUAT NGECEK JAWABAN BACKEND SEBELUM DI-REDIRECT:
        // Hapus tanda // di bawah ini buat ngecek apakah BE beneran nyimpen sebagai guru
        // dd($response->json()); 

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Akun Guru berhasil dibuat! Status saat ini: Inactive (Belum ada Role).');
        }

        $errorMsg = $response->json('message') ?? 'Gagal menambahkan guru ke server.';
        return redirect()->back()->withErrors(['error' => $errorMsg])->withInput();
    }
    public function dataGuru(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        // 1. Tangkap parameter dari URL
        $page = (int) $request->query('page', 1);
        $filterRole = $request->query('role');
        $filterStatus = $request->query('status'); // <-- TAMBAHAN: Tangkap parameter status
        $searchQuery = $request->query('search');
        $perPage = 5;

        // 2. Tembak API
        $response = Http::withToken($apiToken)->get($baseUrl . '/coordinator/teachers');

        if ($response->successful()) {
            $apiData = $response->json();
            $rawData = $apiData['data'] ?? $apiData;

            // Bikin Collection Asli (Untuk Statistik Card)
            $originalCollection = collect($rawData);

            $cardStats = [
                'total_wali_kelas' => $originalCollection->where('role', 'therapist_homeroom')->count(),
                'total_terapis' => $originalCollection->where('role', 'therapist')->count(),
                'total_shadow' => $originalCollection->whereIn('role', ['shadow_teacher', 'shadow_pj'])->count(),
                'total_belum_ditugaskan' => $originalCollection->filter(fn($item) => empty($item['role']))->count(),
            ];

            // Bikin Collection Kedua (Untuk Tabel & Filter)
            $filteredCollection = collect($rawData);

            // LOGIKA FILTER ROLE
            if (!empty($filterRole)) {
                $filteredCollection = $filteredCollection->where('role', $filterRole);
            }

            // LOGIKA FILTER STATUS (Aktif / Inactive) <-- TAMBAHAN FILTER STATUS
            if (!empty($filterStatus)) {
                if ($filterStatus == 'active') {
                    // Tampilkan yang role-nya ADA isinya (Aktif)
                    $filteredCollection = $filteredCollection->filter(fn($item) => !empty($item['role']));
                } elseif ($filterStatus == 'inactive') {
                    // Tampilkan yang role-nya KOSONG (Inactive)
                    $filteredCollection = $filteredCollection->filter(fn($item) => empty($item['role']));
                }
            }

            // LOGIKA SEARCH NAMA / EMAIL
            if (!empty($searchQuery)) {
                $filteredCollection = $filteredCollection->filter(function ($item) use ($searchQuery) {
                    $name = $item['name'] ?? '';
                    $email = $item['email'] ?? '';
                    return stripos($name, $searchQuery) !== false || stripos($email, $searchQuery) !== false;
                });
            }

            // Hitung Pagination dari Data yang sudah di-Filter
            $totalData = $filteredCollection->count();
            $lastPage = (int) ceil($totalData / $perPage);

            $gurus = json_decode(json_encode(
                $filteredCollection->forPage($page, $perPage)->values()->all()
            ));

            $pagination = [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
            ];

            return view('admin.data-guru', compact('gurus', 'pagination', 'cardStats'));
        }

        // Fallback jika gagal
        return view('admin.data-guru', [
            'gurus' => [],
            'pagination' => ['current_page' => 1, 'last_page' => 1],
            'cardStats' => [
                'total_wali_kelas' => 0,
                'total_terapis' => 0,
                'total_shadow' => 0,
                'total_belum_ditugaskan' => 0 // <-- TAMBAHAN: Biar card ke-4 aman kalau error
            ]
        ]);
    }
    // ==========================================
    // UPDATE & AKTIVASI GURU (PUT /users/{id})
    // ==========================================
    public function updateGuru(Request $request, $id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:shadow_pj,shadow_teacher,therapist_homeroom,therapist',
        ]);

        // 2. Siapkan Payload
        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => true // Coba kirim true sekalian di payload
        ];

        // 3. Tembak API Update Utama
        $response = Http::withToken($apiToken)
            ->asJson()
            ->put($baseUrl . '/users/' . $id, $payload);

        if ($response->successful()) {
            $responseData = $response->json('data') ?? [];

            // Jika role sudah masuk tapi is_active masih false, panggil /activate
            if (isset($responseData['is_active']) && $responseData['is_active'] === false) {
                Http::withToken($apiToken)->put($baseUrl . '/users/' . $id . '/activate');
            }

            return redirect()->back()->with('success', 'Role berhasil disimpan dan Akun Guru otomatis Aktif!');
        }

        // Jika gagal
        $errorMsg = $response->json('message') ?? 'Gagal mengupdate data ke server.';
        return redirect()->back()->withErrors(['error' => $errorMsg])->withInput();
    }
    public function destroyGuru($id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        // Tembak API Delete ke Backend (Pastikan endpoint-nya bener ya, biasanya /users/{id})
        $response = Http::withToken($apiToken)->delete($baseUrl . '/users/' . $id);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data Guru/Terapis berhasil dihapus!');
        }

        // Kalau gagal dihapus dari server
        $errorMsg = $response->json('message') ?? 'Gagal menghapus data di server. Pastikan API endpoint benar.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }
}
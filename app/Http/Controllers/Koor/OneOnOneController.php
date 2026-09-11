<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OneOnOneController extends Controller
{
    // ==========================================
    // 1. TAMPILKAN DATA SESI 1 ON 1
    // ==========================================
    public function index(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        // 1. Tangkap parameter search
        $searchQuery = $request->query('search');

        // A. Tarik Data 1 on 1 dari API
        $res1on1 = Http::withToken($apiToken)->get($baseUrl . '/one-on-one-groups');

        $oneOnOnes = [];
        $busyStudentIds = [];
        $busyTeacherIds = [];

        if ($res1on1->successful()) {
            $apiData = $res1on1->json();
            $rawData = $apiData['data'] ?? $apiData ?? [];

            // Kumpulkan ID Siswa & Guru dari data MENTAH (semua data)
            // Biar validasi di dropdown modal tambah/edit tetap akurat
            foreach ($rawData as $sesi) {
                if (isset($sesi['student_id']))
                    $busyStudentIds[] = $sesi['student_id'];
                if (isset($sesi['teacher_id']))
                    $busyTeacherIds[] = $sesi['teacher_id'];
            }

            // Ubah ke Collection buat difilter
            $collection = collect($rawData);

            // ==========================================
            // LOGIKA PENCARIAN (Nama Siswa atau Nama Terapis)
            // ==========================================
            if (!empty($searchQuery)) {
                $collection = $collection->filter(function ($item) use ($searchQuery) {
                    $namaSiswa = $item['student']['name'] ?? '';
                    $namaGuru = $item['teacher']['name'] ?? '';
                    $hari = $item['day_of_week'] ?? ''; // Bonus: bisa nyari 'Senin', 'Selasa', dll.

                    return stripos($namaSiswa, $searchQuery) !== false ||
                        stripos($namaGuru, $searchQuery) !== false ||
                        stripos($hari, $searchQuery) !== false;
                });
            }

            // Convert balik ke array of stdClass buat di Blade
            $oneOnOnes = json_decode(json_encode($collection->values()->all()));
        }

        // B. Tarik Data Semua Siswa
        // Tarik data siswa yang belum punya kelas (unassigned_only = 1)
        $resStudents = Http::withToken($apiToken)->get($baseUrl . '/students', [
            'unassigned_only' => 1
        ]);

        $students = [];
        if ($resStudents->successful()) {
            $studentData = $resStudents->json();
            $rawStudents = $studentData['data'] ?? $studentData ?? [];
            $students = json_decode(json_encode($rawStudents));
        }

        // C. Tarik Data Semua Guru (Terapis)
        $resTeachers = Http::withToken($apiToken)->get($baseUrl . '/users', [
            'role' => 'therapist'
        ]);
        $teachers = [];
        if ($resTeachers->successful()) {
            $teacherData = $resTeachers->json();
            $rawTeachers = $teacherData['data'] ?? $teacherData ?? [];
            $teachers = json_decode(json_encode($rawTeachers));
        }

        // Default pagination (Jika API belum pakai meta pagination)
        $pagination = ['current_page' => 1, 'last_page' => 1];

        // Proteksi variabel agar tidak error di foreach Blade
        if (!is_iterable($oneOnOnes))
            $oneOnOnes = [];
        if (!is_iterable($students))
            $students = [];
        if (!is_iterable($teachers))
            $teachers = [];

        return view('admin.data-1on1', compact(
            'oneOnOnes',
            'students',
            'teachers',
            'busyStudentIds',
            'busyTeacherIds',
            'pagination'
        ));
    }

    public function detail1on1(Request $request, $id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        // Tangkap halaman asal biar pas klik "Back" nggak balik ke hal 1 terus
        $backPage = $request->query('back_page', 1);

        // Tembak detail data ke API Mas Arza
        $response = Http::withToken($apiToken)->get($baseUrl . '/one-on-one-groups/' . $id);

        if ($response->successful()) {
            $apiData = $response->json();

            // Bungkus data biar jadi object stdClass
            $sesi = json_decode(json_encode($apiData['data'] ?? $apiData));

            return view('admin.detail-1on1', compact('sesi', 'backPage'));

            // Catatan: Kalau nama foldermu bukan 'one_on_one', sesuaikan ya, 
            // misal: 'admin.data_1on1.detail' atau 'admin.sessions.detail'
        }
    }

    // ==========================================
    // 2. SIMPAN SESI BARU (POST)
    // ==========================================
    public function store(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|integer',
            'teacher_id' => 'required|integer',
        ]);

        $payload = [
            'name' => $request->name,
            'student_id' => (int) $request->student_id,
            'teacher_id' => (int) $request->teacher_id,
        ];

        $response = Http::withToken($apiToken)->post($baseUrl . '/one-on-one-groups', $payload);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Sesi 1 on 1 berhasil dibuat!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal membuat sesi.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }

    // ==========================================
    // 3. UPDATE SESI (PUT)
    // ==========================================
    public function update(Request $request, $id)
    {
        dd($request->all());
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|integer',
            'teacher_id' => 'required|integer',
        ]);

        $payload = [
            'name' => $request->name,
            'student_id' => (int) $request->student_id,
            'teacher_id' => (int) $request->teacher_id,
        ];

        // --- TAMBAHKAN INI UNTUK DEBUG ---
        $response = Http::withToken($apiToken)->put($baseUrl . '/one-on-one-groups/' . $id, $payload);

        // Cek apa isi respon sebenarnya dari API
        dd($response->json());
        // ---------------------------------

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data sesi 1 on 1 berhasil diupdate!');
        }
        // ... sisa kodenya
    }

    // ==========================================
    // 4. HAPUS SESI (DELETE)
    // ==========================================
    public function destroy($id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.155.13.212/api');

        $response = Http::withToken($apiToken)->delete($baseUrl . '/one-on-one-groups/' . $id);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Sesi 1 on 1 berhasil dihapus!');
        }

        return redirect()->back()->withErrors(['error' => 'Gagal menghapus sesi.']);
    }
}
<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KelasController extends Controller
{
    // ==========================================
    // 1. TAMPILKAN DATA KELAS & GURU
    // ==========================================
    public function dataKelas(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Tangkap parameter pencarian
        $searchQuery = $request->query('search');

        // 2. Tarik Data Kelas dari API
        $kelasRes = Http::withToken($apiToken)->get($baseUrl . '/classes');

        $classes = [];
        $assignedTeacherIds = []; // Menampung ID guru yang sudah jadi wali kelas

        if ($kelasRes->successful()) {
            $apiData = $kelasRes->json();
            $rawData = $apiData['data'] ?? $apiData ?? [];

            // Ubah data jadi Collection buat di-filter
            $collection = collect($rawData);

            // ==========================================
            // LOGIKA PENCARIAN (Berdasarkan Nama Kelas)
            // ==========================================
            if (!empty($searchQuery)) {
                $collection = $collection->filter(function ($item) use ($searchQuery) {
                    $namaKelas = $item['name'] ?? '';
                    return stripos($namaKelas, $searchQuery) !== false;
                });
            }

            // Convert balik ke array of stdClass (format yang dipakai Blade kamu)
            $classes = json_decode(json_encode($collection->values()->all()));

            // Kumpulkan ID Wali Kelas (Dari data yang terfilter & nggak)
            // Biar aman, kita tetep looping dari $classes hasil filter aja
            if (is_array($classes) || is_object($classes)) {
                foreach ($classes as $kelas) {
                    if (isset($kelas->homeroom_teacher_id)) {
                        $assignedTeacherIds[] = $kelas->homeroom_teacher_id;
                    }
                    if (isset($kelas->homeroom_teacher_2_id)) {
                        $assignedTeacherIds[] = $kelas->homeroom_teacher_2_id;
                    }
                }
            }
        }

        // 3. Tarik Data Guru dari API (Khusus Therapist Homeroom)
        $guruRes = Http::withToken($apiToken)->get($baseUrl . '/users', [
            'role' => 'therapist_homeroom'
        ]);

        $teachers = [];
        if ($guruRes->successful()) {
            $guruData = $guruRes->json();
            $rawGuru = $guruData['data'] ?? $guruData ?? [];
            $teachers = json_decode(json_encode($rawGuru));
        }

        // 4. Proteksi variabel agar tidak error di Blade
        if (!is_iterable($classes))
            $classes = [];
        if (!is_iterable($teachers))
            $teachers = [];

        return view('admin.data-kelas', compact('classes', 'teachers', 'assignedTeacherIds'));
    }

    public function storeKelas(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $request->validate([
            'name' => 'required|string|max:255',
            'homeroom_teacher_id' => 'required|integer',
            'homeroom_teacher_2_id' => 'nullable|integer|different:homeroom_teacher_id',
        ]);

        $payload = [
            'name' => $request->name,
            'homeroom_teacher_id' => (int) $request->homeroom_teacher_id,
            'homeroom_teacher_2_id' => $request->homeroom_teacher_2_id ? (int) $request->homeroom_teacher_2_id : null,
        ];

        $response = Http::withToken($apiToken)->post($baseUrl . '/classes', $payload);

        if ($response->successful()) {
            return redirect()->route('koor.dataKelas')->with('success', 'Data Kelas berhasil ditambahkan!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal menambahkan kelas.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }

    public function updateKelas(Request $request, $id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $request->validate([
            'name' => 'required|string|max:255',
            'homeroom_teacher_id' => 'required|integer',
            'homeroom_teacher_2_id' => 'nullable|integer|different:homeroom_teacher_id',
        ]);

        $payload = [
            'name' => $request->name,
            'homeroom_teacher_id' => (int) $request->homeroom_teacher_id,
            'homeroom_teacher_2_id' => $request->homeroom_teacher_2_id ? (int) $request->homeroom_teacher_2_id : null,
        ];

        $response = Http::withToken($apiToken)->put($baseUrl . '/classes/' . $id, $payload);

        if ($response->successful()) {
            return redirect()->route('koor.dataKelas')->with('success', 'Data Kelas berhasil diupdate!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal memperbarui kelas.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }

    public function destroyKelas($id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $response = Http::withToken($apiToken)->delete($baseUrl . '/classes/' . $id);

        if ($response->successful()) {
            return redirect()->route('koor.dataKelas')->with('success', 'Data Kelas berhasil dihapus!');
        }

        return redirect()->back()->withErrors(['error' => 'Gagal menghapus data kelas.']);
    }

    public function detailKelas(Request $request, $id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');
        $backPage = $request->query('back_page', 1);

        // 1. Tembak API untuk ambil detail kelas (termasuk murid yang udah masuk)
        $classResponse = Http::withToken($apiToken)->get($baseUrl . '/classes/' . $id);

        // 2. Tembak API untuk ambil DAFTAR SEMUA SISWA
        $studentResponse = Http::withToken($apiToken)->get($baseUrl . '/students');

        if ($classResponse->successful()) {
            $apiData = $classResponse->json();
            $kelas = json_decode(json_encode($apiData['data'] ?? $apiData));

            // --- PROSES FILTERING SISWA (BIAR NGGAK MUNCUL DOUBLE) ---
            $allStudents = [];
            if ($studentResponse->successful()) {
                $studentData = $studentResponse->json();

                // Jadikan collection biar gampang difilter
                $rawStudents = collect($studentData['data'] ?? $studentData ?? []);

                // Kumpulin ID murid yang udah ada di dalam kelas ini
                $existingStudentIds = collect($kelas->students ?? [])->pluck('id')->toArray();

                // Saring! Cuma ambil murid yang ID-nya NGGAK ADA di dalam $existingStudentIds
                $filteredStudents = $rawStudents->whereNotIn('id', $existingStudentIds)->values();

                // Ubah balik ke bentuk object biar Blade kamu ngga error
                $allStudents = json_decode(json_encode($filteredStudents));
            }

            return view('admin.detail-kelas', compact('kelas', 'backPage', 'allStudents'));
        }

        return redirect()->route('koor.dataKelas')->withErrors(['error' => 'Data kelas tidak ditemukan di server.']);
    }

    public function tambahMuridKeKelas(Request $request, $classId)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Validasi Input (Biar kalau lupa centang ada alert merah)
        $request->validate([
            'student_ids' => 'required|array',
        ], [
            'student_ids.required' => 'Anda belum mencentang satu pun nama siswa.'
        ]);

        // 2. Pastikan ID siswanya diconvert jadi angka (integer) murni
        $payload = [
            'student_ids' => array_map('intval', $request->student_ids)
        ];

        // 3. Tembak ke Endpoint BARU Mas Arza (/attach-students)
        // Pakai asJson() karena kita ngirim tipe data Array, JSON lebih aman dibaca BE
        $response = Http::withToken($apiToken)
            ->asJson()
            ->post($baseUrl . '/classes/' . $classId . '/attach-students', $payload);

        // 4. Cek Response
        if ($response->successful()) {
            // Ambil pesan sukses bawaan dari Backend (misal: "1 murid berhasil ditambahkan...")
            $successMsg = $response->json('message') ?? 'Semua murid berhasil dimasukkan ke kelas!';

            return redirect()->back()->with('success', $successMsg);
        }

        // Tampilkan pesan error asli dari backend kalau gagal
        $errorMsg = $response->json('message') ?? 'Gagal menambahkan murid ke server. Status Code: ' . $response->status();
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }

    // ==========================================
    // 7. KELUARKAN MURID DARI KELAS (DELETE)
    // ==========================================
    public function keluarkanMuridDariKelas($classId, $studentId)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $response = Http::withToken($apiToken)->delete($baseUrl . '/classes/' . $classId . '/students/' . $studentId);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Murid berhasil dikeluarkan dari kelas.');
        }

        return redirect()->back()->withErrors(['error' => 'Gagal mengeluarkan murid.']);
    }
}
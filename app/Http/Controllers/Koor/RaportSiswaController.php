<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RaportSiswaController extends Controller
{
    public function index(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Tangkap parameter dari URL (Page & Search)
        $page = (int) $request->input('page', 1);
        $searchQuery = $request->input('search');
        $perPage = 10; // Tampilkan 10 siswa per halaman

        // 2. Tembak API untuk ambil semua data siswa
        $response = Http::withToken($apiToken)->get($baseUrl . '/students');

        if ($response->successful()) {
            $apiData = $response->json();
            $rawData = $apiData['data'] ?? $apiData ?? [];

            // 3. Jadikan Collection Laravel biar gampang diolah
            $collection = collect($rawData);

            // 4. Fitur LIVE SEARCH (Cari berdasarkan nama siswa)
            if (!empty($searchQuery)) {
                $collection = $collection->filter(function ($item) use ($searchQuery) {
                    $name = $item['name'] ?? '';
                    return stripos($name, $searchQuery) !== false;
                });
            }

            // 5. Pagination Manual (Anti rusak dari Backend)
            $totalData = $collection->count();
            $lastPage = (int) ceil($totalData / $perPage);

            // Ambil data sesuai halaman yang lagi dibuka
            $students = json_decode(json_encode(
                $collection->forPage($page, $perPage)->values()->all()
            ));

            $pagination = [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
            ];

            return view('admin.raport-siswa.index', compact('students', 'pagination'));
        }

        // 6. Fallback kalau API Backend lagi ngambek / error
        return view('admin.raport-siswa.index', [
            'students' => [],
            'pagination' => ['current_page' => 1, 'last_page' => 1]
        ])->withErrors(['error' => 'Gagal mengambil data siswa dari server.']);
    }

    // ==========================================
    // DETAIL RAPOT SISWA
    // ==========================================
    // ==========================================
    // DETAIL RAPOT SISWA
    // ==========================================
    public function detailRaport($id, Request $request) // <-- FIX: Tambah Request $request
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Tangkap parameter dari URL (Page & Search)
        $page = (int) $request->input('page', 1);
        $searchQuery = $request->input('search');
        $perPage = 10; // Tampilkan 10 laporan per halaman

        // 2. Ambil data profil siswa
        $studentResponse = Http::withToken($apiToken)->get($baseUrl . '/students/' . $id);

        if (!$studentResponse->successful()) {
            return redirect()->route('koor.raportSiswa')
                ->withErrors(['error' => 'Data detail siswa tidak ditemukan di server.']);
        }

        $studentData = $studentResponse->json();
        $siswa = json_decode(json_encode($studentData['data'] ?? $studentData));

        // 3. Ambil riwayat daily report (Tarik banyak sekalian biar bisa difilter & paginasi manual)
        $reportResponse = Http::withToken($apiToken)
            ->get($baseUrl . '/coordinator/daily-reports', [
                'student_id' => $id,
                'per_page' => 500, // Tarik banyak agar terbaca semua
            ]);

        $reports = [];
        $pagination = ['current_page' => 1, 'last_page' => 1];

        if ($reportResponse->successful()) {
            $reportData = $reportResponse->json();
            $rawData = $reportData['data'] ?? $reportData ?? [];

            // Jadikan Collection
            $collection = collect($rawData);

            // ==========================================
            // LOGIKA PENCARIAN (Berdasarkan Terapis atau Status)
            // ==========================================
            if (!empty($searchQuery)) {
                $collection = $collection->filter(function ($item) use ($searchQuery) {
                    $guru = $item['teacher']['name'] ?? '';
                    $status = $item['attendance_status'] ?? '';
                    // Format tanggal jika mau dicari
                    $tanggal = \Carbon\Carbon::parse($item['date'] ?? now())->locale('id')->translatedFormat('d F Y');

                    return stripos($guru, $searchQuery) !== false ||
                        stripos($status, $searchQuery) !== false ||
                        stripos($tanggal, $searchQuery) !== false;
                });
            }

            // ==========================================
            // PAGINATION MANUAL
            // ==========================================
            $totalData = $collection->count();
            $lastPage = (int) ceil($totalData / $perPage);
            $page = $page > $lastPage ? max($lastPage, 1) : $page;

            $reports = json_decode(json_encode(
                $collection->forPage($page, $perPage)->values()->all()
            ));

            $pagination = [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
            ];
        }

        // 4. Selipin hasil reports ke object $siswa
        $siswa->reports = $reports;

        // 5. Lempar ke view beserta variabel pagination
        return view('admin.raport-siswa.detail', compact('siswa', 'pagination'));
    }

}
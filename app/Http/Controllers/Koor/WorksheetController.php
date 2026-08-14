<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WorksheetController extends Controller
{
    public function index(Request $request)
    {
        $apiUrl = env('API_BASE_URL', 'http://202.10.44.2/api') . '/worksheets';
        $apiToken = session('api_token');

        // 1. Tangkap parameter dari URL
        $searchQuery = $request->query('search');
        $page = (int) $request->query('page', 1);
        $perPage = 10; // Jumlah data per halaman

        // 2. Tembak API (Pakai per_page = 500 biar data narik semua kayak kasus Guru)
        $response = Http::withToken($apiToken)->get($apiUrl, [
            'per_page' => 500
        ]);

        $worksheets = [];
        $totalAssigned = 0;
        $totalDone = 0;
        $totalPending = 0;
        $pagination = ['current_page' => 1, 'last_page' => 1];

        if ($response->successful()) {
            $apiData = $response->json(); // Ubah ke json array biar gampang di-filter
            $rawData = $apiData['data'] ?? $apiData ?? [];

            if (isset($apiData['stats'])) {
                $totalAssigned = $apiData['stats']['total'] ?? 0;
                $totalDone = $apiData['stats']['submitted'] ?? 0;
                $totalPending = $apiData['stats']['draft'] ?? 0;
            }

            // ==========================================
            // LOGIKA PENCARIAN & FILTERING
            // ==========================================
            $collection = collect($rawData);

            if (!empty($searchQuery)) {
                $collection = $collection->filter(function ($item) use ($searchQuery) {
                    $title = $item['title'] ?? '';
                    $studentName = $item['student']['name'] ?? '';
                    $teacherName = $item['teacher']['name'] ?? '';

                    return stripos($title, $searchQuery) !== false ||
                        stripos($studentName, $searchQuery) !== false ||
                        stripos($teacherName, $searchQuery) !== false;
                });
            }

            // ==========================================
            // PAGINATION MANUAL
            // ==========================================
            $totalData = $collection->count();
            $lastPage = (int) ceil($totalData / $perPage);
            $page = $page > $lastPage ? max($lastPage, 1) : $page;

            // Convert ke Object (stdClass) lagi biar di Blade tetep bisa dipanggil pakai ->
            $worksheets = json_decode(json_encode(
                $collection->forPage($page, $perPage)->values()->all()
            ));

            $pagination = [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
            ];
        }

        // 3. Ambil data Murid (Sudah fix)
        $studentRes = Http::withToken($apiToken)->get(env('API_BASE_URL', 'http://202.10.44.2/api') . '/students');
        $students = [];
        if ($studentRes->successful()) {
            $studentData = $studentRes->object();
            $students = $studentData->data ?? $studentData ?? [];
        }

        // 4. Ambil data Guru dengan role resmi dari Arza: therapist_homeroom
        $teacherRes = Http::withToken($apiToken)->get(env('API_BASE_URL', 'http://202.10.44.2/api') . '/users', [
            'role' => 'therapist_homeroom'
        ]);

        $teachers = [];
        if ($teacherRes->successful()) {
            $teacherData = $teacherRes->object();
            $teachers = $teacherData->data ?? [];
        }

        // Proteksi tipe data agar tidak terjadi error 'on true' lagi di Blade
        if (is_bool($students) || !is_iterable($students))
            $students = [];
        if (is_bool($teachers) || !is_iterable($teachers))
            $teachers = [];
        if (!is_iterable($worksheets))
            $worksheets = []; // Tambahan aman untuk worksheet

        $teacherStats = [];

        // Lempar variabel pagination juga ke view
        return view('admin.worksheet.index', compact(
            'worksheets',
            'totalAssigned',
            'totalDone',
            'totalPending',
            'teacherStats',
            'students',
            'teachers',
            'pagination'
        ));
    }
}
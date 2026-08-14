<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');
        $apiToken = session('api_token') ?: env('API_BEARER_TOKEN');

        $page = $request->input('page', 1);

        // 1. TANGKAP PARAMETER FILTER DARI URL
        $filterStudent = $request->input('student_id');
        $filterDate = $request->input('date');
        $filterMonth = $request->input('month');

        $today = now()->format('Y-m-d');
        $lastMonth = now()->subMonth()->format('Y-m');

        $responses = Http::pool(fn(\Illuminate\Http\Client\Pool $pool) => [
            // 2. MASUKIN FILTER KE DALAM API TABEL
            $pool->as('reports_page')->withToken($apiToken)->get($baseUrl . '/daily-reports', array_filter([
                'page' => $page,
                'per_page' => 5,
                'student_id' => $filterStudent,
                'date' => $filterDate,
                'month' => $filterMonth,
            ])),

            $pool->as('hari_ini')->withToken($apiToken)->get($baseUrl . '/coordinator/daily-reports', ['date' => $today]),
            $pool->as('bulan_lalu')->withToken($apiToken)->get($baseUrl . '/coordinator/daily-reports', ['month' => $lastMonth]),
            $pool->as('total_laporan')->withToken($apiToken)->get($baseUrl . '/coordinator/daily-reports'),
            $pool->as('total_siswa')->withToken($apiToken)->get($baseUrl . '/students') // Pastikan endpoint siswa benar
        ]);

        $reports = [];
        $pagination = ['current_page' => $page, 'last_page' => 1];

        if ($responses['reports_page']->successful()) {
            $apiData = $responses['reports_page']->json();
            $rawData = $apiData['data'] ?? $apiData ?? [];
            $reports = json_decode(json_encode($rawData));

            $meta = $apiData['meta'] ?? [];
            $apiCurrentPage = $meta['current_page'] ?? $apiData['current_page'] ?? null;
            $apiLastPage = $meta['last_page'] ?? $apiData['last_page'] ?? null;

            if ($apiCurrentPage !== null && $apiLastPage !== null) {
                $pagination = ['current_page' => (int) $apiCurrentPage, 'last_page' => (int) $apiLastPage];
            } else {
                $totalData = count($reports);
                $perPage = 5;
                $pagination['last_page'] = (int) ceil($totalData / $perPage) ?: 1;
                $pagination['current_page'] = $page > $pagination['last_page'] ? $pagination['last_page'] : $page;
                $offset = ($pagination['current_page'] - 1) * $perPage;
                $reports = array_slice($reports, $offset, $perPage);
            }
        }

        if (!is_iterable($reports)) {
            $reports = [];
        }

        // 3. AMBIL LIST SISWA UNTUK DROPDOWN FILTER
        $studentsList = [];
        if ($responses['total_siswa']->ok()) {
            // Ambil array data siswanya buat dilempar ke Blade
            $studentsList = json_decode(json_encode($responses['total_siswa']->json() ?? []));
        }



        $totalSiswa = count($studentsList) > 0 ? count($studentsList) : 100;
        if ($totalSiswa <= 0)
            $totalSiswa = 1;

        $laporanHariIni = $responses['hari_ini']->ok() ? ($responses['hari_ini']->json('meta.total') ?? count($responses['hari_ini']->json('data') ?? [])) : 0;
        $laporanBulanLalu = $responses['bulan_lalu']->ok() ? ($responses['bulan_lalu']->json('meta.total') ?? count($responses['bulan_lalu']->json('data') ?? [])) : 0;
        $totalSemuaLaporan = $responses['total_laporan']->ok() ? ($responses['total_laporan']->json('meta.total') ?? count($responses['total_laporan']->json('data') ?? [])) : 0;

        $persenHariIni = round(($laporanHariIni / $totalSiswa) * 100);
        $persenBulanLalu = round(($laporanBulanLalu / $totalSiswa) * 100);

        $cardStats = [
            'hari_ini' => $laporanHariIni,
            'persen_hari_ini' => $persenHariIni > 100 ? 100 : $persenHariIni,
            'bulan_lalu' => $laporanBulanLalu,
            'persen_bulan_lalu' => $persenBulanLalu > 100 ? 100 : $persenBulanLalu,
            'total_laporan' => $totalSemuaLaporan,
            'total_siswa' => $totalSiswa
        ];

        // 4. LEMPAR VARIABEL KE BLADE
        return view('admin.daily_report.index', compact('reports', 'pagination', 'cardStats', 'studentsList'));
    }

    public function detail($id)
    {
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');
        $apiToken = session('api_token') ?: env('API_BEARER_TOKEN');

        // Tembak API spesifik berdasarkan ID Laporan
        $response = Http::withToken($apiToken)->get($baseUrl . '/daily-reports/' . $id);

        if ($response->successful()) {
            $apiData = $response->json();

            // Ambil isinya (menyesuaikan format jika dibungkus 'data')
            $rawData = $apiData['data'] ?? $apiData;

            // Convert array ke Object biar gampang dipanggil di Blade pakai panah (->)
            $report = json_decode(json_encode($rawData));

            // Lempar variabel $report ke view detail yang baru kita buat
            return view('admin.daily_report.detail', compact('report'));
        }

        // Kalau gagal atau data tidak ditemukan, tendang balik ke halaman list
        return redirect()->route('koor.dailyReport.index')->withErrors(['error' => 'Gagal mengambil detail laporan. Data mungkin tidak ditemukan.']);
    }
}
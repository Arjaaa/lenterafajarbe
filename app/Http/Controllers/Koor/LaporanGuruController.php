<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LaporanGuruController extends Controller
{
    private function baseUrl()
    {
        return env('API_BASE_URL', 'http://202.10.44.2/api');
    }

    // ==========================================
    // 1. HALAMAN INDEX (Daftar Rapor + Statistik)
    // ==========================================
    public function index(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = $this->baseUrl();

        $page = $request->input('page', 1);

        // Ambil data laporan bulanan (sudah termasuk info teacher, has_feedback, dll)
        $response = Http::withToken($apiToken)->get($baseUrl . '/coordinator/teacher-reports', [
            'page' => $page,
            'per_page' => 5,
        ]);

        $teachers = [];
        $stats = [
            'feedback_given' => 0,
            'feedback_total' => 0,
            'last_month_reports' => 0,
            'last_month_feedback' => 0,
            'total_reports' => 0,
            'total_guru' => 0,
        ];
        $pagination = ['current_page' => 1, 'last_page' => 1];

        if ($response->successful()) {
            $apiData = $response->json();

            // Data laporan (id = report id, ada juga field teacher{}, has_feedback, dll)
            $teachers = json_decode(json_encode($apiData['data'] ?? []));

            $stats = array_merge($stats, $apiData['stats'] ?? []);

            if (isset($apiData['pagination'])) {
                $pagination = [
                    'current_page' => $apiData['pagination']['current_page'] ?? 1,
                    'last_page' => $apiData['pagination']['last_page'] ?? 1,
                ];
            }
        }

        if (!is_iterable($teachers)) {
            $teachers = [];
        }

        return view('admin.rapor-guru-index', compact('teachers', 'pagination', 'stats'));
    }

    // ==========================================
    // 2. HALAMAN DETAIL RAPOR (Spesifik 1 Guru)
    // ==========================================
    public function detailRapor($id)
    {
        $apiToken = session('api_token');
        $baseUrl = $this->baseUrl();

        // $id di sini = REPORT ID (bukan teacher id)
        $response = Http::withToken($apiToken)->get($baseUrl . '/teacher-reports/monthly/' . $id);

        $rapor = null;

        if ($response->successful()) {
            $apiData = $response->json();
            $rapor = json_decode(json_encode($apiData['data'] ?? null));
        }

        if (!$rapor) {
            return redirect()->route('koor.raporGuru')
                ->withErrors(['error' => 'Data rapor guru tidak ditemukan di server.']);
        }

        return view('admin.rapor-guru-detail', compact('rapor'));
    }
    // ==========================================
    // 3. SIMPAN FEEDBACK / REKOMENDASI (BARU)
    // ==========================================
    public function storeFeedback(Request $request, $reportId)
    {
        $request->validate([
            'coordinator_recommendation' => 'required|string|max:1000',
            'performance_indicator' => 'sometimes|in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ]);

        $apiToken = session('api_token');
        $baseUrl = $this->baseUrl();

        $payload = [
            'coordinator_recommendation' => $request->input('coordinator_recommendation'),
        ];

        // opsional, cuma kirim kalau ada
        if ($request->filled('performance_indicator')) {
            $payload['performance_indicator'] = $request->input('performance_indicator');
        }

        $response = Http::withToken($apiToken)->put(
            $baseUrl . '/teacher-reports/monthly/' . $reportId . '/recommendation',
            $payload
        );

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Feedback berhasil dikirim ke guru.');
        }

        return redirect()->back()->withErrors([
            'error' => 'Gagal mengirim feedback. ' . ($response->json('message') ?? 'Silakan coba lagi.'),
        ]);
    }
}
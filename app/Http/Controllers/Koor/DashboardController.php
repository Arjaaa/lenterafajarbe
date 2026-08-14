<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Definisikan nilai bawaan (default 0)
        $totalAnak = 0;
        $totalWaliKelas = 0;
        $totalTerapis1on1 = 0;
        $totalShadowTeacher = 0;

        $totalKelasUmum = 0;
        $total1on1 = 0;
        $totalGroupShadow = 0;

        $apiUrl = env('API_BASE_URL', 'http://202.10.44.2/api') . '/coordinator/dashboard';
        $apiToken = session('api_token');

        // 2. Tembak endpoint resmi Arza
        $response = Http::withToken($apiToken)->get($apiUrl);

        if ($response->successful()) {
            $apiData = $response->object();
            $data = $apiData->data ?? null;

            if ($data) {
                // 3. Ekstraksi Bagian Kartu Statistik (Summary) sesuai JSON baru
                if (isset($data->summary) && is_array($data->summary)) {
                    foreach ($data->summary as $item) {
                        if ($item->key === 'total_siswa')
                            $totalAnak = $item->value;
                        if ($item->key === 'total_wali_kelas')
                            $totalWaliKelas = $item->value;
                        if ($item->key === 'total_terapis_1on1')
                            $totalTerapis1on1 = $item->value;
                        if ($item->key === 'total_shadow_teacher')
                            $totalShadowTeacher = $item->value;
                    }
                }

                // 4. Ekstraksi Bagian Sebaran Penempatan sesuai JSON baru
                if (isset($data->sebaran_penempatan) && is_array($data->sebaran_penempatan)) {
                    foreach ($data->sebaran_penempatan as $item) {
                        if ($item->key === 'kelas_terapis')
                            $totalKelasUmum = $item->value;
                        if ($item->key === 'sesi_one_on_one')
                            $total1on1 = $item->value;
                        if ($item->key === 'group_shadow_teacher')
                            $totalGroupShadow = $item->value;
                    }
                }
            }
        }

        // 5. Kirim data ke view dashboard sesuai nama variabel di Blade
        return view('admin.dashboard', compact(
            'totalAnak',
            'totalWaliKelas',
            'totalTerapis1on1',
            'totalShadowTeacher',
            'totalKelasUmum',
            'total1on1',
            'totalGroupShadow'
        ));
    }
}



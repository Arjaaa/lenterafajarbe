<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class ShadowController extends Controller
{
    // ==========================================
    // 1. TAMPILKAN DATA GROUP SHADOW
    // ==========================================
    public function dataShadowGroup(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // 1. Tangkap parameter search
        $searchQuery = $request->query('search');

        // A. Tarik Data Group Shadow dari API
        $resShadow = Http::withToken($apiToken)->get($baseUrl . '/shadow-groups');

        $shadowGroups = [];
        $busyStudentIds = [];
        $assignedPicIds = [];
        $assignedPartnerIds = [];

        if ($resShadow->successful()) {
            $apiData = $resShadow->json();
            $rawData = $apiData['data'] ?? $apiData ?? [];

            // Kumpulkan ID Anak & Guru yang sudah kebagian tugas DARI DATA MENTAH
            foreach ($rawData as $group) {
                if (isset($group['student_id']))
                    $busyStudentIds[] = $group['student_id'];
                if (isset($group['pic_id']))
                    $assignedPicIds[] = $group['pic_id'];
                if (isset($group['partner_id']))
                    $assignedPartnerIds[] = $group['partner_id'];
            }

            // Ubah ke Collection buat difilter
            $collection = collect($rawData);

            // ==========================================
            // LOGIKA PENCARIAN (Siswa, PIC, atau Partner)
            // ==========================================
            if (!empty($searchQuery)) {
                $collection = $collection->filter(function ($item) use ($searchQuery) {
                    $namaSiswa = $item['student']['name'] ?? '';
                    $namaPic = $item['pic']['name'] ?? '';
                    $namaPartner = $item['partner']['name'] ?? '';

                    return stripos($namaSiswa, $searchQuery) !== false ||
                        stripos($namaPic, $searchQuery) !== false ||
                        stripos($namaPartner, $searchQuery) !== false;
                });
            }

            // Convert balik ke array of stdClass buat di Blade
            $shadowGroups = json_decode(json_encode($collection->values()->all()));
        }

        // B. Tarik Data Siswa
        $resStudents = Http::withToken($apiToken)->get($baseUrl . '/students');
        $students = [];
        if ($resStudents->successful()) {
            $studentData = $resStudents->json();
            $students = json_decode(json_encode($studentData['data'] ?? $studentData ?? []));
        }

        // C. Tarik Data PJ Shadow
        $resPj = Http::withToken($apiToken)->get($baseUrl . '/users', [
            'role' => 'shadow_pj'
        ]);
        $pjs = [];
        if ($resPj->successful()) {
            $pjData = $resPj->json();
            $pjs = json_decode(json_encode($pjData['data'] ?? $pjData ?? []));
        }

        // D. Tarik Data Guru Shadow (Partner)
        $resPartner = Http::withToken($apiToken)->get($baseUrl . '/users', [
            'role' => 'shadow_teacher'
        ]);
        $partners = [];
        if ($resPartner->successful()) {
            $partnerData = $resPartner->json();
            $partners = json_decode(json_encode($partnerData['data'] ?? $partnerData ?? []));
        }

        // Default pagination jika belum ada dari API
        $pagination = ['current_page' => 1, 'last_page' => 1];

        // Proteksi variabel biar nggak error foreach di Blade
        if (!is_iterable($shadowGroups))
            $shadowGroups = [];
        if (!is_iterable($students))
            $students = [];
        if (!is_iterable($pjs))
            $pjs = [];
        if (!is_iterable($partners))
            $partners = [];

        // Pastikan nama file Blade kamu 'admin/data-shadow.blade.php'
        return view('admin.data-shadow', compact(
            'shadowGroups',
            'students',
            'pjs',
            'partners',
            'busyStudentIds',
            'assignedPicIds',
            'assignedPartnerIds',
            'pagination'
        ));
    }
    public function detailShadowGroup(Request $request, $id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        // Tangkap halaman asal
        $backPage = $request->query('back_page', 1);

        // Nembak API ambil detail group shadow
        $response = Http::withToken($apiToken)->get($baseUrl . '/shadow-groups/' . $id);

        if ($response->successful()) {
            $apiData = $response->json();

            $rawData = $apiData['data'] ?? $apiData;
            $group = json_decode(json_encode($rawData));

            return view('admin.detail-shadow', compact('group', 'backPage'));
        }

        return redirect()->route('koor.dataShadowGroup')->withErrors(['error' => 'Data group tidak ditemukan di server.']);
    }

    // ==========================================
    // 2. SIMPAN GROUP SHADOW BARU
    // ==========================================
    public function storeShadowGroup(Request $request)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|integer',
            'pic_id' => 'required|integer',
            'partner_id' => 'required|integer',
            'school_name' => 'required|string|max:255',
        ]);

        $payload = [
            'name' => $request->name,
            'student_id' => (int) $request->student_id,
            'pic_id' => (int) $request->pic_id,
            'partner_id' => (int) $request->partner_id,
            'school_name' => $request->school_name,
        ];

        $response = Http::withToken($apiToken)->post($baseUrl . '/shadow-groups', $payload);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Group Shadow Teacher berhasil dibuat!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal membuat group shadow.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }

    // ==========================================
    // 3. UPDATE GROUP SHADOW
    // ==========================================
    public function updateShadowGroup(Request $request, $id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|integer',
            'pic_id' => 'required|integer',
            'partner_id' => 'required|integer',
            'school_name' => 'required|string|max:255',
        ]);

        $payload = [
            'name' => $request->name,
            'student_id' => (int) $request->student_id,
            'pic_id' => (int) $request->pic_id,
            'partner_id' => (int) $request->partner_id,
            'school_name' => $request->school_name,
        ];

        $response = Http::withToken($apiToken)->put($baseUrl . '/shadow-groups/' . $id, $payload);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Group Shadow Teacher berhasil diupdate!');
        }

        $errorMsg = $response->json('message') ?? 'Gagal memperbarui group shadow.';
        return redirect()->back()->withErrors(['error' => $errorMsg]);
    }

    // ==========================================
    // 4. HAPUS GROUP SHADOW
    // ==========================================
    public function destroyShadowGroup($id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $response = Http::withToken($apiToken)->delete($baseUrl . '/shadow-groups/' . $id);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Group Shadow Teacher berhasil dihapus!');
        }

        return redirect()->back()->withErrors(['error' => 'Gagal menghapus group shadow.']);
    }

    // ==========================================
    // 5. DETAIL SHADOW (Opsional, karena sudah pakai Modal)
    // ==========================================
    public function show($id)
    {
        $apiToken = session('api_token');
        $baseUrl = env('API_BASE_URL', 'http://202.10.44.2/api');

        $response = Http::withToken($apiToken)->get($baseUrl . '/shadow-groups/' . $id);

        if ($response->successful()) {
            $apiData = $response->json();
            $shadow = json_decode(json_encode($apiData['data'] ?? $apiData));

            return view('admin.detail-shadow', compact('shadow'));
        }

        return redirect()->route('koor.dataShadowGroup')->withErrors(['error' => 'Data detail tidak ditemukan di server.']);
    }
}
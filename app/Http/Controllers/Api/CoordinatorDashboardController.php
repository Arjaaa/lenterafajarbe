<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\ShadowGroup;
use App\Models\OneOnOneGroup;
use Illuminate\Http\Request;

class CoordinatorDashboardController extends Controller
{
    // GET /api/coordinator/dashboard
    public function index()
    {
        // ── Total Pengguna ────────────────────────────────────────────────────
        $totalAnak      = Student::count();
        $totalOrangTua  = User::where('role', 'parent')->count();
        $totalGuru      = User::whereIn('role', [
            'shadow_pj', 'shadow_teacher',
            'therapist_homeroom', 'therapist',
            'coordinator_main', 'coordinator_therapist',
            'coordinator_shadow', 'coordinator_wil',
        ])->count();
        $totalKelas     = ClassRoom::count();
        $totalGroup     = ShadowGroup::count() + OneOnOneGroup::count();

        // ── Sebaran Penempatan ────────────────────────────────────────────────
        $kelasReguler  = ClassRoom::withCount('students')->get()
            ->sum('students_count');
        $groupShadow   = ShadowGroup::count();
        $sesiOneOnOne  = OneOnOneGroup::count();

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    [
                        'title'    => 'Total Anak',
                        'value'    => $totalAnak,
                        'key'      => 'total_anak',
                        'subtitle' => 'Terdaftar',
                    ],
                    [
                        'title'    => 'Total Orang Tua',
                        'value'    => $totalOrangTua,
                        'key'      => 'total_orang_tua',
                        'subtitle' => 'Akun Aktif',
                    ],
                    [
                        'title'    => 'Total Guru & Terapis',
                        'value'    => $totalGuru,
                        'key'      => 'total_guru',
                        'subtitle' => 'Siap Bertugas',
                    ],
                    [
                        'title'    => 'Total Kelas & Grup',
                        'value'    => $totalKelas + $totalGroup,
                        'key'      => 'total_kelas_grup',
                        'subtitle' => 'Sedang Berjalan',
                    ],
                ],
                'sebaran_penempatan' => [
                    [
                        'title' => 'Kelas Reguler',
                        'value' => $kelasReguler,
                        'key'   => 'kelas_reguler',
                    ],
                    [
                        'title' => 'Group Shadow',
                        'value' => $groupShadow,
                        'key'   => 'group_shadow',
                    ],
                    [
                        'title' => 'Sesi 1 on 1',
                        'value' => $sesiOneOnOne,
                        'key'   => 'sesi_one_on_one',
                    ],
                ],
            ],
        ]);
    }
}
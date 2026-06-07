<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\ShadowGroup;
use App\Models\OneOnOneGroup;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total user
        $totalAnak = Student::count();
        $totalOrtu = User::where('role', 'parent')->count();
        $totalGuru = User::whereIn('role', ['shadow_pj', 'shadow_teacher', 'therapist_homeroom', 'therapist'])->count();

        // Hitung total kelas/grup aktif
        $totalKelasUmum = ClassRoom::count();
        $totalGroupShadow = ShadowGroup::count();
        $total1on1 = OneOnOneGroup::count();
        $totalKelasAktif = $totalKelasUmum + $totalGroupShadow + $total1on1;

        return view('admin.dashboard', compact(
            'totalAnak',
            'totalOrtu',
            'totalGuru',
            'totalKelasAktif',
            'totalKelasUmum',
            'totalGroupShadow',
            'total1on1'
        ));
    }
}
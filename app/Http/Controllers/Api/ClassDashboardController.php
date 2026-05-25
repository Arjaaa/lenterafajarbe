<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClassDashboardController extends Controller
{
    // Avatar colors untuk siswa — di-assign berdasarkan index
    private array $avatarColors = [
        '#7C6EF5', // ungu
        '#F5A623', // oranye
        '#52C41A', // hijau
        '#4A90E2', // biru
        '#F5222D', // merah
        '#13C2C2', // teal
        '#EB2F96', // pink
        '#FA8C16', // oranye tua
    ];

    // GET /api/class-dashboard
    public function index(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today();
        $week  = Carbon::now()->startOfWeek();

        $classes = ClassRoom::with([
            'homeroomTeacher:id,name',
            'students:id,name,gender,birth_date',
        ])->get();

        $totalSiswa = $classes->sum(fn($c) => $c->students->count());
        $kelasAktif = $classes->filter(fn($c) => $c->students->count() > 0)->count();

        $laporanMingguIni = DailyReport::whereBetween('date', [$week, Carbon::now()])->count();

        $sudahLaporHariIni = DailyReport::whereDate('date', $today)
            ->distinct('student_id')
            ->count('student_id');

        $kehadiranPersen = $totalSiswa > 0
            ? round(($sudahLaporHariIni / $totalSiswa) * 100)
            : 0;

        $daftarKelas = $classes->map(function ($kelas) use ($today) {
            $studentIds     = $kelas->students->pluck('id');
            $totalSiswa     = $studentIds->count();

            $sudahLapor = DailyReport::whereDate('date', $today)
                ->whereIn('student_id', $studentIds)
                ->distinct('student_id')
                ->count('student_id');

            $belumLapor     = max(0, $totalSiswa - $sudahLapor);
            $progressPersen = $totalSiswa > 0
                ? round(($sudahLapor / $totalSiswa) * 100)
                : 0;

            return [
                'id'               => $kelas->id,
                'name'             => $kelas->name,
                'homeroom_teacher' => $kelas->homeroomTeacher?->name ?? '-',
                'total_students'   => $totalSiswa,
                'sudah_lapor'      => $sudahLapor,
                'belum_lapor'      => $belumLapor,
                'progress_persen'  => $progressPersen,
                'status'           => 'Aktif',
            ];
        });

        $aktivitasTerkini = DailyReport::whereDate('date', $today)
            ->with([
                'student:id,name',
                'student.classes:id,name',
            ])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($r) => [
                'report_id'    => $r->id,
                'student_name' => $r->student?->name ?? '-',
                'class_name'   => $r->student?->classes?->first()?->name ?? '-',
                'action'       => 'Laporan harian ditambahkan',
                'time_ago'     => $r->created_at->diffForHumans(),
                'avatar'       => strtoupper(substr($r->student?->name ?? 'X', 0, 1)),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diambil.',
            'data'    => [
                'statistics' => [
                    ['title' => 'Kelas aktif',       'value' => $kelasAktif,            'key' => 'active_classes'],
                    ['title' => 'Total siswa',        'value' => $totalSiswa,            'key' => 'total_students'],
                    ['title' => 'Laporan minggu ini', 'value' => $laporanMingguIni,      'key' => 'weekly_reports'],
                    ['title' => 'Kehadiran hari ini', 'value' => $kehadiranPersen . '%', 'key' => 'today_attendance'],
                ],
                'total_classes'     => $classes->count(),
                'classes'           => $daftarKelas,
                'recent_activities' => $aktivitasTerkini,
            ],
        ]);
    }

    // GET /api/class-dashboard/{classId}
    public function show(Request $request, $classId)
    {
        $today = Carbon::today();

        $kelas = ClassRoom::with([
            'homeroomTeacher:id,name',
            'students:id,name,gender,birth_date,photo',
        ])->findOrFail($classId);

        $students   = $kelas->students;
        $studentIds = $students->pluck('id');
        $totalSiswa = $students->count();

        // Laporan hari ini untuk kelas ini
        $laporanHariIni = DailyReport::whereDate('date', $today)
            ->whereIn('student_id', $studentIds)
            ->get(['id', 'student_id', 'created_at', 'therapist_id', 'shadow_teacher_id']);

        $sudahLaporIds  = $laporanHariIni->pluck('student_id')->toArray();
        $reportMap      = $laporanHariIni->keyBy('student_id');

        $sudahLapor      = count($sudahLaporIds);
        $belumLapor      = max(0, $totalSiswa - $sudahLapor);
        $kehadiranPersen = $totalSiswa > 0 ? round(($sudahLapor / $totalSiswa) * 100) : 0;

        $kegiatanHariIni = DailyReport::whereDate('date', $today)
            ->whereIn('student_id', $studentIds)
            ->whereHas('detail', fn($q) => $q->whereNotNull('activity_notes')
                ->where('activity_notes', '!=', ''))
            ->count();

        // Daftar siswa dengan avatar color
        $daftarSiswa = $students->values()->map(function ($siswa, $index) use ($sudahLaporIds, $reportMap) {
            $umur = $siswa->birth_date
                ? Carbon::parse($siswa->birth_date)->age . ' tahun'
                : '-';

 $gender = match ($siswa->gender) {
    'laki-laki' => 'Laki-laki',
    'perempuan' => 'Perempuan',
    default     => '-',
};

            $sudah = in_array($siswa->id, $sudahLaporIds);

            return [
                'id'     => $siswa->id,
                'name'   => $siswa->name,
                'avatar' => [
                    'initial' => strtoupper(substr($siswa->name, 0, 1)),
                    'color'   => $this->avatarColors[$index % count($this->avatarColors)],
                    'photo'   => $siswa->photo ?: null,
                ],
                'gender'        => $gender,
                'age'           => $umur,
                'report_status' => $sudah ? 'sudah_laporan' : 'belum_laporan',
                'report_id'     => $reportMap[$siswa->id]?->id ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Detail kelas berhasil diambil.',
            'data'    => [
                'class' => [
                    'id'               => $kelas->id,
                    'name'             => $kelas->name,
                    'subtitle'         => 'Wali kelas · ' . ($kelas->homeroomTeacher?->name ?? '-') . ' · ' . $totalSiswa . ' siswa',
                    'homeroom_teacher' => $kelas->homeroomTeacher?->name ?? '-',
                    'total_students'   => $totalSiswa,
                ],
                'statistics' => [
                    ['title' => 'Laporan hari ini', 'value' => $sudahLapor,            'key' => 'today_reports'],
                    ['title' => 'Belum laporan',    'value' => $belumLapor,            'key' => 'not_reported'],
                    ['title' => 'Kegiatan hari ini','value' => $kegiatanHariIni,       'key' => 'today_activities'],
                    ['title' => 'Kehadiran',        'value' => $kehadiranPersen . '%', 'key' => 'attendance'],
                ],
                'total_students' => $totalSiswa,
                'students'       => $daftarSiswa,
            ],
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClassDashboardController extends Controller
{
    private array $avatarColors = [
        '#7C6EF5', '#F5A623', '#52C41A', '#4A90E2',
        '#F5222D', '#13C2C2', '#EB2F96', '#FA8C16',
    ];

    // GET /api/class-dashboard
    public function index(Request $request)
    {
        $today     = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        $classes = ClassRoom::with([
            'homeroomTeacher:id,name',
            'students:id,name,gender,birth_date',
        ])->get();

        // Total semua siswa
        $totalSiswa = $classes->sum(fn($c) => $c->students->count());

        // Semua student IDs
        $allStudentIds = $classes->flatMap(fn($c) => $c->students->pluck('id'))->unique()->values();

        // Laporan hari ini (dihitung dari siswa unik yang sudah lapor)
        $sudahLaporCount = DailyReport::whereDate('date', $today)
            ->whereIn('student_id', $allStudentIds)
            ->distinct('student_id')
            ->count('student_id');

        // ── Daftar kelas (tetap lengkap per kelas) ────────────────────────────
        $daftarKelas = $classes->map(function ($kelas) use ($today, $weekStart, $weekEnd) {
            $studentIds = $kelas->students->pluck('id');
            $totalStudents = $studentIds->count();

            $sudahLapor = DailyReport::whereDate('date', $today)
                ->whereIn('student_id', $studentIds)
                ->distinct('student_id')
                ->count('student_id');

            $laporanMinggu = DailyReport::whereBetween('date', [$weekStart, $weekEnd])
                ->whereIn('student_id', $studentIds)
                ->count();

            $belumLapor     = max(0, $totalStudents - $sudahLapor);
            $progressPersen = $totalStudents > 0 ? round(($sudahLapor / $totalStudents) * 100) : 0;

            return [
                'id'                 => $kelas->id,
                'name'               => $kelas->name,
                'homeroom_teacher'   => $kelas->homeroomTeacher?->name ?? '-',
                'total_students'     => $totalStudents,
                'sudah_lapor'        => $sudahLapor,
                'belum_lapor'        => $belumLapor,
                'progress_persen'    => $progressPersen,
                'laporan_minggu_ini' => $laporanMinggu,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                // ── Ringkasan (buat 2 card atas + badge jumlah kelas) ──────
                'summary' => [
                    'total_siswa'      => $totalSiswa,
                    'laporan_hari_ini' => $sudahLaporCount,
                    'total_kelas'      => $classes->count(),
                ],
                // ── Daftar kelas (lengkap, buat list "Daftar kelas") ───────
                'classes' => $daftarKelas,
            ],
        ]);
    }

    // GET /api/class-dashboard/{classId}  (atau /api/student-list/{classId})
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
            ->get(['id', 'student_id', 'attendance_status', 'created_at']);

        $reportMap     = $laporanHariIni->keyBy('student_id');
        $sudahLapor    = $laporanHariIni->pluck('student_id')->unique()->count();
        $belumLapor    = max(0, $totalSiswa - $sudahLapor);

        // Kehadiran hari ini — yang attendance_status = hadir
        $hadirCount      = $laporanHariIni->where('attendance_status', 'hadir')->count();
        $kehadiranPersen = $totalSiswa > 0 ? round(($hadirCount / $totalSiswa) * 100) : 0;

        // Label status attendance
        $statusLabel = [
            'hadir'  => 'Hadir',
            'sakit'  => 'Sakit',
            'izin'   => 'Izin',
            'alpha'  => 'Alpha',
        ];

        // Daftar siswa
        $daftarSiswa = $students->values()->map(function ($siswa, $index) use ($reportMap, $statusLabel) {
            $umur   = $siswa->birth_date ? Carbon::parse($siswa->birth_date)->age . ' tahun' : '-';
            $gender = match ($siswa->gender) {
                'laki-laki' => 'Laki-laki',
                'perempuan' => 'Perempuan',
                default     => '-',
            };

            $report           = $reportMap[$siswa->id] ?? null;
            $sudah            = !is_null($report);
            $attendanceStatus = $report?->attendance_status;

            return [
                'id'     => $siswa->id,
                'name'   => $siswa->name,
                'avatar' => [
                    'initial' => strtoupper(substr($siswa->name, 0, 1)),
                    'color'   => $this->avatarColors[$index % count($this->avatarColors)],
                    'photo'   => $siswa->photo ?: null,
                ],
                'gender'             => $gender,
                'age'                => $umur,
                'attendance_status'  => $attendanceStatus,
                'attendance_label'   => $sudah ? ($statusLabel[$attendanceStatus] ?? 'Hadir') : 'Belum diisi',
                'report_id'          => $report?->id ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                // ── Info kelas ────────────────────────────────────────────
                'class' => [
                    'id'               => $kelas->id,
                    'name'             => $kelas->name,
                    'homeroom_teacher' => $kelas->homeroomTeacher?->name ?? '-',
                    'subtitle'         => 'Wali kelas · ' . ($kelas->homeroomTeacher?->name ?? '-') . ' · ' . $totalSiswa . ' siswa',
                    'total_students'   => $totalSiswa,
                ],
                // ── Stats ─────────────────────────────────────────────────
                'laporan_hari_ini' => [
                    'sudah' => $sudahLapor,
                    'belum' => $belumLapor,
                    'label' => "{$sudahLapor}/{$totalSiswa}",
                ],
                'kehadiran_hari_ini' => [
                    'hadir' => $hadirCount,
                    'label' => $kehadiranPersen . '%',
                ],
                // ── Daftar siswa ──────────────────────────────────────────
                'students' => $daftarSiswa,
            ],
        ]);
    }
}
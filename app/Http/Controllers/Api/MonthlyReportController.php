<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Services\MonthlyReportService;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    public function __construct(protected MonthlyReportService $service) {}

    // GET /api/monthly-reports
    public function index(Request $request)
    {
        $query = MonthlyReport::with('student:id,name,photo')->latest();

        if ($request->has('student_id')) $query->where('student_id', $request->student_id);
        if ($request->has('month'))      $query->where('month', $request->month);
        if ($request->has('year'))       $query->where('year', $request->year);

        $reports = $query->get()->map(fn($r) => $this->formatReport($r));

        return response()->json(['success' => true, 'data' => $reports]);
    }

    // GET /api/monthly-reports/{id}
    public function show($id)
    {
        $report = MonthlyReport::with('student:id,name,photo')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $this->formatReport($report)]);
    }

    // GET /api/monthly-reports/student/{studentId}
    public function byStudent(Request $request, $studentId)
    {
        $query = MonthlyReport::where('student_id', $studentId)
            ->with('student:id,name,photo')
            ->latest();

        if ($request->has('year')) $query->where('year', $request->year);

        $reports = $query->get()->map(fn($r) => $this->formatReport($r));
        return response()->json(['success' => true, 'data' => $reports]);
    }

    // POST /api/monthly-reports/generate
    public function generate(Request $request)
    {
        $request->validate([
            'month'      => 'nullable|integer|min:1|max:12',
            'year'       => 'nullable|integer|min:2024',
            'student_id' => 'nullable|exists:students,id',
        ]);

        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        if ($request->has('student_id')) {
            $report = $this->service->generate($request->student_id, $month, $year);
            $report->load('student:id,name,photo');

            return response()->json([
                'success' => true,
                'message' => 'Laporan bulanan berhasil digenerate.',
                'data'    => $this->formatReport($report),
            ]);
        }

        $results = $this->service->generateForAllStudents($month, $year);
        return response()->json(['success' => true, 'message' => 'Generate selesai.', 'results' => $results]);
    }

    // PUT /api/monthly-reports/{id}/coordinator-note
    public function coordinatorNote(Request $request, $id)
    {
        $request->validate(['coordinator_note' => 'required|string']);

        $report = MonthlyReport::findOrFail($id);
        $report->update(['coordinator_note' => $request->coordinator_note]);
        $report->load('student:id,name,photo');

        return response()->json([
            'success' => true,
            'message' => 'Catatan koordinator berhasil disimpan.',
            'data'    => $this->formatReport($report),
        ]);
    }

    // GET /api/parent/children/{studentId}/monthly-reports
    public function parentView(Request $request, $studentId)
    {
        \App\Models\Student::where('id', $studentId)
            ->where('parent_id', $request->user()->id)
            ->firstOrFail();

        $reports = MonthlyReport::where('student_id', $studentId)
            ->where('status', 'generated')
            ->with('student:id,name,photo')
            ->orderByDesc('year')->orderByDesc('month')
            ->get()->map(fn($r) => $this->formatReport($r));

        return response()->json(['success' => true, 'data' => $reports]);
    }

    // ─── Format Report ────────────────────────────────────────────────────────

    private function formatReport(MonthlyReport $report): array
    {
        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $student      = $report->student;
        $nameParts    = explode(' ', $student->name);
        $avatar       = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
        $class        = $student->classes?->first();
        $academicYear = $report->month >= 7
            ? $report->year . '/' . ($report->year + 1)
            : ($report->year - 1) . '/' . $report->year;

        // ── Attendance breakdown ──────────────────────────────────────────────
        $attendanceBreakdown = [
            'hadir' => $report->attendance_stats['hadir']['count'] ?? 0,
            'sakit' => $report->attendance_stats['sakit']['count'] ?? 0,
            'izin'  => $report->attendance_stats['izin']['count']  ?? 0,
            'alpha' => $report->attendance_stats['alpha']['count'] ?? 0,
        ];
        $presentDays = $attendanceBreakdown['hadir'];
        $absentDays  = $attendanceBreakdown['sakit'] + $attendanceBreakdown['izin'] + $attendanceBreakdown['alpha'];

        // ── Development radar ─────────────────────────────────────────────────
        $developmentRadar   = $this->buildDevelopmentRadar($report);
        $strongestDimension = collect($developmentRadar)->sortByDesc('percentage')->first()['key'] ?? null;

        // ── Achievements ──────────────────────────────────────────────────────
        $achievements = $this->buildAchievements($report);

        // ── Mood heatmap ──────────────────────────────────────────────────────
        $moodHeatmap = $this->buildMoodHeatmap($report);

        return [
            'student' => [
                'id'            => $student->id,
                'name'          => $student->name,
                'photo'         => $student->photo,
                'avatar'        => $avatar,
                'class'         => $class?->name,
                'academic_year' => $academicYear,
            ],
            'period' => [
                'month'       => $report->month,
                'year'        => $report->year,
                'label'       => $bulanIndo[$report->month] . ' ' . $report->year,
                'range_label' => '1 bulan',
            ],
            'summary' => [
                'total_reports'        => $report->total_reports,
                'homework_days'        => $report->total_homework_days,
                'no_homework_days'     => $report->total_no_homework_days,
                'challenge_days'       => $report->total_challenges,
                'mood_arrival_avg'     => (float) $report->mood_arrival_avg,
                'mood_end_avg'         => (float) $report->mood_end_avg,
                'present_days'         => $presentDays,
                'absent_days'          => $absentDays,
                'attendance_breakdown' => $attendanceBreakdown,
            ],
            'attendance' => $this->formatStats($report->attendance_stats, [
                'hadir' => ['label' => 'Hadir', 'color' => '#52C41A'],
                'sakit' => ['label' => 'Sakit', 'color' => '#F5A623'],
                'izin'  => ['label' => 'Izin',  'color' => '#4A90E2'],
                'alpha' => ['label' => 'Alpha', 'color' => '#FF4D4F'],
            ]),
            'physical_condition' => [
                'arrival'    => $this->formatStats($report->physical_condition_stats),
                'going_home' => $this->formatStats($report->physical_condition_end_stats),
            ],
            'physical_energy' => [
                'arrival'    => $this->formatStats($report->physical_energy_arrival_stats),
                'going_home' => $this->formatStats($report->physical_energy_end_stats),
            ],
            'independence'      => $this->formatStats($report->independence_stats),
            'mood' => [
                'arrival_avg'      => (float) $report->mood_arrival_avg,
                'end_avg'          => (float) $report->mood_end_avg,
                'arrival_emoji'    => $this->moodToEmoji((float) $report->mood_arrival_avg),
                'end_emoji'        => $this->moodToEmoji((float) $report->mood_end_avg),
                'arrival_label'    => $this->moodToLabel((float) $report->mood_arrival_avg),
                'end_label'        => $this->moodToLabel((float) $report->mood_end_avg),
                'trend'            => $this->formatStats($report->mood_trend_stats),
                'arrival_dominant' => $this->formatMoodDominant($report->mood_arrival_dominant),
                'end_dominant'     => $this->formatMoodDominant($report->mood_end_dominant),
                'positive_stats'   => [
                    'positive' => [
                        'label'      => 'Mood Positif',
                        'count'      => $report->mood_positive_stats['positive']['count'] ?? 0,
                        'percentage' => (float) ($report->mood_positive_stats['positive']['percent'] ?? 0),
                    ],
                    'neutral_negative' => [
                        'label'      => 'Mood Netral / Kurang Baik',
                        'count'      => $report->mood_positive_stats['neutral_negative']['count'] ?? 0,
                        'percentage' => (float) ($report->mood_positive_stats['neutral_negative']['percent'] ?? 0),
                    ],
                ],
            ],
            'behavior'          => $this->formatStats($report->behavior_stats),
            'activity_response' => $this->formatStats($report->response_stats),
            'common_problems'   => $this->formatStats($report->challenge_stats),
            'overall_score'     => $this->formatStats($report->overall_score_stats),
            'homework' => [
                'days_with_homework'    => $report->total_homework_days,
                'days_without_homework' => $report->total_no_homework_days,
            ],
            'activities'  => $this->formatTextStats($report->activity_stats),
            'solutions'   => $this->formatTextStats($report->solution_stats),
            'communication' => [
                'mode'       => $this->formatStats($report->communication_mode_stats, [
                    'verbal'     => ['label' => 'Verbal',     'color' => '#52C41A'],
                    'non_verbal' => ['label' => 'Non Verbal', 'color' => '#4A90E2'],
                    'gesture'    => ['label' => 'Gestur',     'color' => '#F5A623'],
                    'aac'        => ['label' => 'AAC',        'color' => '#722ED1'],
                ]),
                'initiative' => $this->formatStats($report->communication_initiative_stats, [
                    'often'     => ['label' => 'Sering',        'color' => '#52C41A'],
                    'sometimes' => ['label' => 'Kadang-kadang', 'color' => '#F5A623'],
                    'rarely'    => ['label' => 'Jarang',        'color' => '#FF4D4F'],
                ]),
            ],
            'social' => [
                'with_teacher' => $this->formatStats($report->social_with_teacher_stats, [
                    'responsive'          => ['label' => 'Responsif',      'color' => '#52C41A'],
                    'needs_encouragement' => ['label' => 'Perlu Dorongan', 'color' => '#F5A623'],
                    'refusing'            => ['label' => 'Menolak',        'color' => '#FF4D4F'],
                ]),
                'with_peers' => $this->formatStats($report->social_with_peers_stats, [
                    'active'   => ['label' => 'Aktif',      'color' => '#52C41A'],
                    'passive'  => ['label' => 'Pasif',      'color' => '#F5A623'],
                    'avoiding' => ['label' => 'Menghindar', 'color' => '#FF4D4F'],
                ]),
            ],
            'achievement_stats' => $this->formatStats($report->achievement_tag_stats, [
                'first_time'  => ['label' => 'Pertama Kali', 'color' => '#722ED1'],
                'improvement' => ['label' => 'Ada Kemajuan', 'color' => '#52C41A'],
                'consistent'  => ['label' => 'Konsisten',    'color' => '#4A90E2'],
            ]),
            'development_radar'   => $developmentRadar,
            'strongest_dimension' => $strongestDimension,
            'achievements'        => $achievements,
            'mood_heatmap'        => $moodHeatmap,
            'ai_insight' => [
                'summary'         => $report->ai_summary,
                'attention'       => $report->ai_attention,
                'recommendation'  => $report->ai_recommendation,
                'headline'        => $report->ai_headline,
                'headline_emoji'  => $report->ai_headline_emoji,
                'attention_trend' => $report->ai_attention_trend,
                'attention_note'  => $report->ai_attention_note,
            ],
            'coordinator_note' => $report->coordinator_note,
            'meta' => [
                'report_id'    => $report->id,
                'status'       => $report->status,
                'generated_at' => $report->generated_at,
                'updated_at'   => $report->updated_at,
            ],
        ];
    }

    // ─── Build Development Radar ──────────────────────────────────────────────

    private function buildDevelopmentRadar(MonthlyReport $report): array
    {
        $presentDays = $report->attendance_stats['hadir']['count'] ?? $report->total_reports;
        if ($presentDays === 0) $presentDays = 1;

        // 1. Komunikasi verbal
        $verbalCount      = $report->communication_mode_stats['verbal']['count'] ?? 0;
        $communicationPct = round(($verbalCount / $presentDays) * 100, 1);

        // 2. Interaksi sosial
        $socialScoreMap = [
            'responsive' => 100, 'active' => 100,
            'needs_encouragement' => 60, 'passive' => 60,
            'refusing' => 20, 'avoiding' => 20,
        ];

        $totalSocialScore = 0;
        $socialCount      = 0;

        foreach ($report->social_with_teacher_stats ?? [] as $key => $val) {
            $score = $socialScoreMap[$key] ?? 60;
            $totalSocialScore += $score * ($val['count'] ?? 0);
            $socialCount += $val['count'] ?? 0;
        }
        foreach ($report->social_with_peers_stats ?? [] as $key => $val) {
            $score = $socialScoreMap[$key] ?? 60;
            $totalSocialScore += $score * ($val['count'] ?? 0);
            $socialCount += $val['count'] ?? 0;
        }
        $socialPct = $socialCount > 0 ? round($totalSocialScore / $socialCount, 1) : 0;

        // 3. Kemandirian
        $independenceScoreMap = [
            'sangat_mandiri' => 100,
            'mandiri'        => 80,
            'perlu_bantuan'  => 40,
            'lainnya'        => 50,
        ];
        $totalIndScore = 0;
        $indCount      = 0;
        foreach ($report->independence_stats ?? [] as $key => $val) {
            $score = $independenceScoreMap[$key] ?? 50;
            $totalIndScore += $score * ($val['count'] ?? 0);
            $indCount += $val['count'] ?? 0;
        }
        $independencePct = $indCount > 0 ? round($totalIndScore / $indCount, 1) : 0;

        // 4. Regulasi emosi — inverse dari % behavior negatif
        $negativeBehaviors     = ['mudah_terdistraksi', 'lainnya'];
        $negativeBehaviorCount = 0;
        foreach ($report->behavior_stats ?? [] as $key => $val) {
            if (in_array($key, $negativeBehaviors)) {
                $negativeBehaviorCount += $val['count'] ?? 0;
            }
        }
        $emotionPct = $presentDays > 0
            ? round((1 - ($negativeBehaviorCount / $presentDays)) * 100, 1)
            : 0;
        $emotionPct = max(0, min(100, $emotionPct));

        // 5. Respons kegiatan
        $responseScoreMap = [
            'antusias'         => 100,
            'pasif'            => 50,
            'perlu_arahan'     => 40,
            'perlu_pengawasan' => 30,
            'lainnya'          => 50,
        ];
        $totalRespScore = 0;
        $respCount      = 0;
        foreach ($report->response_stats ?? [] as $key => $val) {
            $score = $responseScoreMap[$key] ?? 50;
            $totalRespScore += $score * ($val['count'] ?? 0);
            $respCount += $val['count'] ?? 0;
        }
        $activityResponsePct = $respCount > 0 ? round($totalRespScore / $respCount, 1) : 0;

        $getColor = fn($pct) => $pct >= 75 ? '#34C759' : ($pct >= 50 ? '#FF9500' : '#FF3B30');

        return [
            ['key' => 'communication',    'label' => 'Komunikasi verbal', 'percentage' => $communicationPct,    'color' => '#007AFF'],
            ['key' => 'social',           'label' => 'Interaksi sosial',  'percentage' => $socialPct,           'color' => '#007AFF'],
            ['key' => 'independence',     'label' => 'Kemandirian',       'percentage' => $independencePct,     'color' => $getColor($independencePct)],
            ['key' => 'emotion',          'label' => 'Regulasi emosi',    'percentage' => $emotionPct,          'color' => $getColor($emotionPct)],
            ['key' => 'activity_response','label' => 'Respons kegiatan',  'percentage' => $activityResponsePct, 'color' => $getColor($activityResponsePct)],
        ];
    }

    // ─── Build Achievements ───────────────────────────────────────────────────

    private function buildAchievements(MonthlyReport $report): array
    {
        $tagLabels = [
            'first_time'  => 'Pertama kali',
            'improvement' => 'Peningkatan',
            'consistent'  => 'Konsisten',
        ];

        $dailyReports = \App\Models\DailyReport::with(['detail'])
            ->where('student_id', $report->student_id)
            ->whereMonth('date', $report->month)
            ->whereYear('date', $report->year)
            ->whereHas('detail', fn($q) => $q->whereNotNull('achievement_note'))
            ->orderByDesc('date')
            ->get();

        return $dailyReports->map(function ($r) use ($tagLabels) {
            return [
                'id'        => $r->detail->id,
                'date'      => $r->date->toDateString(),
                'note'      => $r->detail->achievement_note,
                'tag'       => $r->detail->achievement_tag,
                'tag_label' => $tagLabels[$r->detail->achievement_tag] ?? null,
            ];
        })->values()->toArray();
    }

    // ─── Build Mood Heatmap ───────────────────────────────────────────────────

    private function buildMoodHeatmap(MonthlyReport $report): array
    {
        // Ambil semua laporan bulan ini, key by tanggal
        $dailyReports = \App\Models\DailyReport::with('detail')
            ->where('student_id', $report->student_id)
            ->whereMonth('date', $report->month)
            ->whereYear('date', $report->year)
            ->orderBy('date')
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        $heatmap   = [];
        $dayNumber = 1;

        $startDate = \Carbon\Carbon::createFromDate($report->year, $report->month, 1);
        $endDate   = $startDate->copy()->endOfMonth();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekend()) continue;

            $dateStr     = $date->toDateString();
            $dailyReport = $dailyReports->get($dateStr);

            if ($dailyReport) {
                // ✅ FIX: cek attendance_status langsung dari kolom, bukan relasi
                $isAbsent  = $dailyReport->attendance_status !== 'hadir';
                $moodScore = null;

                if (!$isAbsent && $dailyReport->detail) {
                    $arr = $dailyReport->detail->mood_arrival;
                    $end = $dailyReport->detail->mood_end;
                    if ($arr && $end) {
                        $moodScore = round(($arr + $end) / 2, 1);
                    } elseif ($arr) {
                        $moodScore = (float) $arr;
                    } elseif ($end) {
                        $moodScore = (float) $end;
                    }
                }

                $heatmap[] = [
                    'date'       => $dateStr,
                    'day_number' => $dayNumber,
                    'mood_score' => $moodScore,
                    'status'     => $isAbsent ? 'absent' : 'present',
                ];

                $dayNumber++;
            }
        }

        return $heatmap;
    }

    // ─── Format Stats ─────────────────────────────────────────────────────────

    private function formatStats(?array $stats, array $customMap = []): array
    {
        if (empty($stats)) return [];

        $colorMap = [
            'sehat'=>'#52C41A','sedikit_lelah'=>'#F5A623','kurang_fit'=>'#FF7A45','mengantuk'=>'#FF4D4F',
            'ceria'=>'#52C41A','aktif'=>'#4A90E2','tenang'=>'#F5A623','lelah'=>'#FF4D4F',
            'kooperatif'=>'#52C41A','fokus'=>'#4A90E2','mudah_terdistraksi'=>'#FF4D4F',
            'antusias'=>'#52C41A','pasif'=>'#F5A623','perlu_arahan'=>'#FF7A45','perlu_pengawasan'=>'#FF4D4F',
            'kurang_fokus'=>'#F5A623','mood_kurang_stabil'=>'#FF7A45','sulit_diarahkan'=>'#FF4D4F',
            'sangat_baik'=>'#237804','baik'=>'#52C41A','cukup'=>'#F5A623','kurang'=>'#FF7A45','sangat_kurang'=>'#FF4D4F',
            'naik'=>'#52C41A','stabil'=>'#F5A623','turun'=>'#FF4D4F',
            'mandiri'=>'#52C41A','cukup_mandiri'=>'#F5A623','perlu_bantuan'=>'#FF7A45','sangat_mandiri'=>'#52C41A',
            'lainnya'=>'#8C8C8C',
        ];

        $labelMap = [
            'sehat'=>'Sehat','sedikit_lelah'=>'Sedikit Lelah','kurang_fit'=>'Kurang Fit','mengantuk'=>'Mengantuk',
            'ceria'=>'Ceria','aktif'=>'Aktif','tenang'=>'Tenang','lelah'=>'Lelah',
            'kooperatif'=>'Kooperatif','fokus'=>'Fokus','mudah_terdistraksi'=>'Mudah Terdistraksi',
            'antusias'=>'Antusias','pasif'=>'Pasif','perlu_arahan'=>'Perlu Arahan','perlu_pengawasan'=>'Perlu Pengawasan',
            'kurang_fokus'=>'Kurang Fokus','mood_kurang_stabil'=>'Mood Kurang Stabil','sulit_diarahkan'=>'Sulit Diarahkan',
            'sangat_baik'=>'Sangat Baik','baik'=>'Baik','cukup'=>'Cukup','kurang'=>'Kurang','sangat_kurang'=>'Sangat Kurang',
            'naik'=>'Membaik','stabil'=>'Stabil','turun'=>'Menurun',
            'mandiri'=>'Mandiri','cukup_mandiri'=>'Cukup Mandiri','perlu_bantuan'=>'Perlu Bantuan','sangat_mandiri'=>'Sangat Mandiri',
            'lainnya'=>'Lainnya',
        ];

        return collect($stats)
            ->map(fn($val, $key) => [
                'key'        => $key,
                'label'      => $customMap[$key]['label'] ?? $labelMap[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                'count'      => $val['count'] ?? 0,
                'percentage' => (float) ($val['percent'] ?? 0),
                'color'      => $customMap[$key]['color'] ?? $colorMap[$key] ?? '#8C8C8C',
            ])
            ->sortByDesc('percentage')
            ->values()
            ->toArray();
    }

    private function formatTextStats(?array $stats): array
    {
        if (empty($stats)) return [];

        return collect($stats)
            ->map(fn($val, $key) => [
                'label'      => $key,
                'count'      => $val['count'] ?? 0,
                'percentage' => (float) ($val['percent'] ?? 0),
            ])
            ->sortByDesc('percentage')
            ->values()
            ->toArray();
    }

    private function formatMoodDominant(?array $dominant): array
    {
        if (empty($dominant)) return [];

        $moodEmoji = [
            'Sangat Senang' => '😄', 'Senang' => '😊',
            'Biasa' => '😐', 'Sedih' => '😔', 'Sangat Sedih' => '😢',
        ];

        return collect($dominant)
            ->map(fn($val, $key) => [
                'label'      => $key,
                'emoji'      => $moodEmoji[$key] ?? '😐',
                'count'      => $val['count'] ?? 0,
                'percentage' => (float) ($val['percent'] ?? 0),
            ])
            ->sortByDesc('percentage')
            ->values()
            ->toArray();
    }

    private function moodToEmoji(float $avg): string
    {
        return match(true) {
            $avg >= 4.5 => '😄', $avg >= 3.5 => '😊',
            $avg >= 2.5 => '😐', $avg >= 1.5 => '😔',
            default     => '😢',
        };
    }

    private function moodToLabel(float $avg): string
    {
        return match(true) {
            $avg >= 4.5 => 'Sangat Baik', $avg >= 3.5 => 'Baik',
            $avg >= 2.5 => 'Cukup',       $avg >= 1.5 => 'Kurang',
            default     => 'Sangat Kurang',
        };
    }
}
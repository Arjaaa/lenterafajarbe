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
        $request->validate([
            'coordinator_note' => 'required|string',
        ]);

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


    private function formatReport(MonthlyReport $report): array
    {
        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $student   = $report->student;
        $nameParts = explode(' ', $student->name);
        $avatar    = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

        return [
            'student' => [
                'id'     => $student->id,
                'name'   => $student->name,
                'photo'  => $student->photo,
                'avatar' => $avatar,
            ],
            'period' => [
                'month'       => $report->month,
                'year'        => $report->year,
                'label'       => $bulanIndo[$report->month] . ' ' . $report->year,
                'range_label' => '1 bulan',
            ],
            'summary' => [
                'total_reports'    => $report->total_reports,
                'homework_days'    => $report->total_homework_days,
                'no_homework_days' => $report->total_no_homework_days,
                'challenge_days'   => $report->total_challenges,
                'mood_arrival_avg' => (float) $report->mood_arrival_avg,
                'mood_end_avg'     => (float) $report->mood_end_avg,
            ],
            'physical_condition' => [
                'arrival'    => $this->formatStats($report->physical_condition_stats),
                'going_home' => $this->formatStats($report->physical_condition_end_stats),
            ],
            'physical_energy' => [
                'arrival'    => $this->formatStats($report->physical_energy_arrival_stats),
                'going_home' => $this->formatStats($report->physical_energy_end_stats),
            ],
            'independence' => $this->formatStats($report->independence_stats),
            'mood' => [
                'arrival_avg'   => (float) $report->mood_arrival_avg,
                'end_avg'       => (float) $report->mood_end_avg,
                'arrival_emoji' => $this->moodToEmoji((float) $report->mood_arrival_avg),
                'end_emoji'     => $this->moodToEmoji((float) $report->mood_end_avg),
                'arrival_label' => $this->moodToLabel((float) $report->mood_arrival_avg),
                'end_label'     => $this->moodToLabel((float) $report->mood_end_avg),
                'trend'         => $this->formatStats($report->mood_trend_stats),
            ],
            'behavior'          => $this->formatStats($report->behavior_stats),
            'activity_response' => $this->formatStats($report->response_stats),
            'common_problems'   => $this->formatStats($report->challenge_stats),
            'overall_score'     => $this->formatStats($report->overall_score_stats),
            'homework' => [
                'days_with_homework'    => $report->total_homework_days,
                'days_without_homework' => $report->total_no_homework_days,
            ],
            'ai_insight' => [
                'summary'        => $report->ai_summary,
                'attention'      => $report->ai_attention,
                'recommendation' => $report->ai_recommendation,
            ],
            'coordinator_note' => $report->coordinator_note,  // ← tambahan
            'coordinator_note' => $report->coordinator_note,
            'meta' => [
                'report_id'    => $report->id,
                'status'       => $report->status,
                'generated_at' => $report->generated_at,
                'updated_at'   => $report->updated_at,
            ],
        ];
    }


    private function formatStats(?array $stats): array
    {
        if (empty($stats)) return [];

        $colorMap = [
            'sehat'=>'#52C41A','sedikit_lelah'=>'#F5A623','kurang_fit'=>'#FF7A45','mengantuk'=>'#FF4D4F',
            'ceria'=>'#52C41A','aktif'=>'#4A90E2','tenang'=>'#F5A623','lelah'=>'#FF4D4F',
            'kooperatif'=>'#52C41A','fokus'=>'#4A90E2','mudah_terdistraksi'=>'#FF4D4F',
            'antusias'=>'#52C41A','pasif'=>'#F5A623','perlu_arahan'=>'#FF7A45','perlu_pengawasan'=>'#FF4D4F',
            'kurang_fokus'=>'#F5A623','mood_kurang_stabil'=>'#FF7A45','sulit_diarahkan'=>'#FF4D4F',
            'sangat_baik'=>'#237804','baik'=>'#52C41A','cukup'=>'#F5A623','kurang'=>'#FF7A45','sangat_kurang'=>'#FF4D4F',
            'naik'=>'#52C41A','stabil'=>'#F5A623','turun'=>'#FF4D4F','mandiri'=>'#52C41A','cukup_mandiri'=>'#F5A623','perlu_bantuan'=>'#FF7A45','sangat_tergantung'=>'#FF4D4F',
            'energik'=>'#52C41A','segar'=>'#4A90E2','biasa'=>'#F5A623',
            'lainnya'=>'#8C8C8C',
        ];

        $labelMap = [
            'sehat'=>'Sehat','sedikit_lelah'=>'Sedikit Lelah','kurang_fit'=>'Kurang Fit','mengantuk'=>'Mengantuk',
            'ceria'=>'Ceria','aktif'=>'Aktif','tenang'=>'Tenang','lelah'=>'Lelah',
            'kooperatif'=>'Kooperatif','fokus'=>'Fokus','mudah_terdistraksi'=>'Mudah Terdistraksi',
            'antusias'=>'Antusias','pasif'=>'Pasif','perlu_arahan'=>'Perlu Arahan','perlu_pengawasan'=>'Perlu Pengawasan',
            'kurang_fokus'=>'Kurang Fokus','mood_kurang_stabil'=>'Mood Kurang Stabil','sulit_diarahkan'=>'Sulit Diarahkan',
            'sangat_baik'=>'Sangat Baik','baik'=>'Baik','cukup'=>'Cukup','kurang'=>'Kurang','sangat_kurang'=>'Sangat Kurang',
            'naik'=>'Membaik','stabil'=>'Stabil','turun'=>'Menurun','mandiri'=>'Mandiri','cukup_mandiri'=>'Cukup Mandiri','perlu_bantuan'=>'Perlu Bantuan','sangat_tergantung'=>'Sangat Tergantung',
            'energik'=>'Energik','segar'=>'Segar','biasa'=>'Biasa',
            'lainnya'=>'Lainnya',
        ];

        return collect($stats)
            ->map(fn($val, $key) => [
                'key'        => $key,
                'label'      => $labelMap[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                'count'      => $val['count'] ?? 0,
                'percentage' => (float) ($val['percent'] ?? 0),
                'color'      => $colorMap[$key] ?? '#8C8C8C',
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
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Student;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    const PHYSICAL_CONDITION     = ['sehat', 'sedikit_lelah', 'kurang_fit', 'mengantuk', 'lainnya'];
    const PHYSICAL_CONDITION_END = ['sehat', 'sedikit_lelah', 'kurang_fit', 'mengantuk', 'lainnya'];
    const PHYSICAL_ENERGY        = ['ceria', 'aktif', 'lelah', 'tenang', 'lainnya'];
    const BEHAVIOR               = ['kooperatif', 'fokus', 'aktif', 'mudah_terdistraksi', 'lainnya'];
    const RESPONSE               = ['antusias', 'pasif', 'perlu_arahan', 'perlu_pengawasan', 'lainnya'];
    const CHALLENGE              = ['tidak_ada_kendala','kurang_fokus', 'mudah_terdistraksi', 'mood_kurang_stabil', 'sulit_diarahkan', 'lainnya'];
    const INDEPENDENCE           = ['mandiri', 'perlu_bantuan', 'sangat_mandiri', 'lainnya'];
    const ATTENDANCE_STATUS      = ['hadir', 'sakit', 'izin', 'alpha'];
    const ACHIEVEMENT_TAG        = ['first_time', 'improvement', 'consistent'];
    const COMMUNICATION_MODE     = ['verbal', 'non_verbal', 'gesture', 'aac'];
    const COMMUNICATION_INITIATIVE = ['often', 'sometimes', 'rarely'];
    const SOCIAL_WITH_TEACHER    = ['responsive', 'needs_encouragement', 'refusing'];
    const SOCIAL_WITH_PEERS      = ['active', 'passive', 'avoiding'];

    private function uploadToCloudinary($file, string $folder): string
    {
        $mimeType = $file->getMimeType();
        $isVideo  = str_starts_with($mimeType, 'video/');

        if ($isVideo) {
            $videoService   = app(\App\Services\VideoCompressionService::class);
            $compressedPath = $videoService->compress($file->getRealPath());

            $uploaded = cloudinary()->upload($compressedPath, [
                'folder'        => 'guru-report/' . $folder,
                'resource_type' => 'video',
            ]);

            if (file_exists($compressedPath)) {
                unlink($compressedPath);
            }
        } else {
            $uploaded = cloudinary()->upload($file->getRealPath(), [
                'folder'         => 'guru-report/' . $folder,
                'resource_type'  => 'image',
                'transformation' => [
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                    'width'        => 1200,
                    'crop'         => 'limit',
                ],
            ]);
        }

        return $uploaded->getSecurePath();
    }

    private function deleteFromCloudinary(?string $url): void
    {
        if (!$url) return;

        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z0-9]+$/i', $url, $matches);
        if (empty($matches[1])) return;

        $publicId = $matches[1];

        try {
            cloudinary()->destroy($publicId, ['resource_type' => 'image']);
        } catch (\Exception $e) {
            try {
                cloudinary()->destroy($publicId, ['resource_type' => 'video']);
            } catch (\Exception $e) {
                // Abaikan jika gagal hapus
            }
        }
    }

    private function uploadMultiple(array $files, string $folder): array
    {
        $urls = [];
        foreach (array_slice($files, 0, 3) as $index => $file) {
            \Log::info("Uploading file {$index} to {$folder}", [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);
            try {
                $urls[] = $this->uploadToCloudinary($file, $folder);
                \Log::info("Success file {$index}");
            } catch (\Exception $e) {
                \Log::error("Failed file {$index}: " . $e->getMessage());
            }
        }
        return $urls;
    }

    private function deleteMultipleFromCloudinary(?array $urls): void
    {
        if (empty($urls)) return;
        foreach ($urls as $url) {
            $this->deleteFromCloudinary($url);
        }
    }

    // GET /api/daily-reports
    public function index(Request $request)
    {
        $query = DailyReport::with([
            'detail',
            'student:id,name',
            'shadowTeacher:id,name,role',
            'therapist:id,name,role',
        ])->latest('date');

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('month')) {
            $query->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$request->month]);
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        return response()->json($query->get());
    }

    // GET /api/daily-reports/{id}
    public function show($id)
    {
        $report = DailyReport::with([
            'detail',
            'student:id,name',
            'shadowTeacher:id,name,role',
            'therapist:id,name,role',
        ])->findOrFail($id);

        return response()->json($report);
    }

    // POST /api/daily-reports
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate([
            'student_id'                      => 'required|exists:students,id',
            'date'                            => 'required|date',
            'attendance_status'               => 'required|in:' . implode(',', self::ATTENDANCE_STATUS),
            'physical_condition_arrival'      => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION),
            'physical_condition_end'          => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION_END),
            'physical_energy_arrival'         => 'nullable|in:' . implode(',', self::PHYSICAL_ENERGY),
            'physical_energy_end'             => 'nullable|in:' . implode(',', self::PHYSICAL_ENERGY),
            'independence'                    => 'nullable|in:' . implode(',', self::INDEPENDENCE),
            'mood_arrival'                    => 'nullable|integer|min:1|max:5',
            'mood_end'                        => 'nullable|integer|min:1|max:5',
            'behavior'                        => 'nullable|in:' . implode(',', self::BEHAVIOR),
            'activity_notes'                  => 'nullable|string',
            'response'                        => 'nullable|in:' . implode(',', self::RESPONSE),
            'challenge'                       => 'nullable|in:' . implode(',', self::CHALLENGE),
            'challenge_other'                 => 'nullable|string|max:255|required_if:challenge,lainnya',
            'physical_condition_other'        => 'nullable|string|max:255|required_if:physical_condition_arrival,lainnya',
            'physical_condition_end_other'    => 'nullable|string|max:255|required_if:physical_condition_end,lainnya',
            'physical_energy_arrival_other'   => 'nullable|string|max:255|required_if:physical_energy_arrival,lainnya',
            'physical_energy_end_other'       => 'nullable|string|max:255|required_if:physical_energy_end,lainnya',
            'independence_other'              => 'nullable|string|max:255|required_if:independence,lainnya',
            'behavior_other'                  => 'nullable|string|max:255|required_if:behavior,lainnya',
            'response_other'                  => 'nullable|string|max:255|required_if:response,lainnya',
            'solution_notes'                  => 'nullable|string',
            'has_homework'                    => 'nullable|boolean',
            'homework_detail'                 => 'nullable|string',
            'achievement_note'                => 'nullable|string|max:500',
            'achievement_tag'                 => 'nullable|in:' . implode(',', self::ACHIEVEMENT_TAG),
            'communication_mode'              => 'nullable|in:' . implode(',', self::COMMUNICATION_MODE),
            'communication_initiative'        => 'nullable|in:' . implode(',', self::COMMUNICATION_INITIATIVE),
            'social_with_teacher'             => 'nullable|in:' . implode(',', self::SOCIAL_WITH_TEACHER),
            'social_with_peers'               => 'nullable|in:' . implode(',', self::SOCIAL_WITH_PEERS),
            'photo_physical'                  => 'nullable|array|max:3',
            'photo_physical.*'                => 'file|max:51200',
            'photo_activity'                  => 'nullable|array|max:3',
            'photo_activity.*'                => 'file|max:51200',
            'photo_other'                     => 'nullable|array|max:3',
            'photo_other.*'                   => 'file|max:51200',
        ]);

        $exists = DailyReport::where('student_id', $request->student_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Laporan untuk murid ini pada tanggal tersebut sudah ada.',
            ], 422);
        }

        $shadowTeacherId = null;
        $therapistId     = null;

        if (in_array($user->role, ['shadow_pj', 'shadow_teacher'])) {
            $shadowTeacherId = $user->id;
        } elseif (in_array($user->role, ['therapist_homeroom', 'therapist'])) {
            $therapistId = $user->id;
        }

        $attendanceStatus = $request->attendance_status;
        $isAbsent         = $attendanceStatus !== 'hadir';

        $report = DailyReport::create([
            'student_id'        => $request->student_id,
            'shadow_teacher_id' => $shadowTeacherId,
            'therapist_id'      => $therapistId,
            'date'              => $request->date,
            'attendance_status' => $attendanceStatus,
        ]);

        // Skip detail & klasifikasi kalau tidak hadir
        if (!$isAbsent) {
            $photoPhysical = $request->hasFile('photo_physical')
                ? $this->uploadMultiple($request->file('photo_physical'), 'physical')
                : [];

            $photoActivity = $request->hasFile('photo_activity')
                ? $this->uploadMultiple($request->file('photo_activity'), 'activity')
                : [];

            $photoOther = $request->hasFile('photo_other')
                ? $this->uploadMultiple($request->file('photo_other'), 'other')
                : [];

            $textFields = collect([
                $request->activity_notes,
                $request->solution_notes,
                $request->homework_detail,
                $request->achievement_note,
            ])->filter()->implode(' ');

            $report->detail()->create([
                'physical_condition_arrival'    => $request->physical_condition_arrival,
                'physical_condition_other'      => $request->physical_condition_arrival === 'lainnya' ? $request->physical_condition_other : null,
                'physical_condition_end'        => $request->physical_condition_end,
                'physical_condition_end_other'  => $request->physical_condition_end === 'lainnya' ? $request->physical_condition_end_other : null,
                'physical_energy_arrival'       => $request->physical_energy_arrival,
                'physical_energy_arrival_other' => $request->physical_energy_arrival === 'lainnya' ? $request->physical_energy_arrival_other : null,
                'physical_energy_end'           => $request->physical_energy_end,
                'physical_energy_end_other'     => $request->physical_energy_end === 'lainnya' ? $request->physical_energy_end_other : null,
                'independence'                  => $request->independence,
                'independence_other'            => $request->independence === 'lainnya' ? $request->independence_other : null,
                'mood_arrival'                  => $request->mood_arrival,
                'mood_end'                      => $request->mood_end,
                'behavior'                      => $request->behavior,
                'behavior_other'                => $request->behavior === 'lainnya' ? $request->behavior_other : null,
                'activity_notes'                => $request->activity_notes,
                'response'                      => $request->response,
                'response_other'                => $request->response === 'lainnya' ? $request->response_other : null,
                'challenge'                     => $request->challenge,
                'challenge_other'               => $request->challenge === 'lainnya' ? $request->challenge_other : null,
                'solution_notes'                => $request->solution_notes,
                'has_homework'                  => $request->has_homework ?? false,
                'homework_detail'               => $request->homework_detail,
                'achievement_note'              => $request->achievement_note,
                'achievement_tag'               => $request->achievement_tag,
                'communication_mode'            => $request->communication_mode,
                'communication_initiative'      => $request->communication_initiative,
                'social_with_teacher'           => $request->social_with_teacher,
                'social_with_peers'             => $request->social_with_peers,
                'photo_physical'                => $photoPhysical,
                'photo_activity'                => $photoActivity,
                'photo_other'                   => $photoOther,
                'text_length'                   => str_word_count($textFields),
            ]);

            $report->load('detail');
            app(\App\Services\ReportClassificationService::class)->classify($report);
        }

        return response()->json([
            'message' => 'Laporan harian berhasil disimpan.',
            'report'  => $report->load([
                'detail',
                'classification',
                'student:id,name',
                'shadowTeacher:id,name,role',
                'therapist:id,name,role',
            ]),
        ], 201);
    }

    // POST /api/daily-reports/{id} (update)
    public function update(Request $request, $id)
    {
        $report = DailyReport::with('detail')->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        $isOwner = $report->shadow_teacher_id === $user->id || $report->therapist_id === $user->id;
        if (!$isOwner && !$user->isCoordinator()) {
            return response()->json(['message' => 'Anda tidak berhak mengedit laporan ini.'], 403);
        }

        $request->validate([
            'attendance_status'             => 'nullable|in:' . implode(',', self::ATTENDANCE_STATUS),
            'physical_condition_arrival'    => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION),
            'physical_condition_end'        => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION_END),
            'physical_energy_arrival'       => 'nullable|in:' . implode(',', self::PHYSICAL_ENERGY),
            'physical_energy_end'           => 'nullable|in:' . implode(',', self::PHYSICAL_ENERGY),
            'independence'                  => 'nullable|in:' . implode(',', self::INDEPENDENCE),
            'mood_arrival'                  => 'nullable|integer|min:1|max:5',
            'mood_end'                      => 'nullable|integer|min:1|max:5',
            'behavior'                      => 'nullable|in:' . implode(',', self::BEHAVIOR),
            'activity_notes'                => 'nullable|string',
            'response'                      => 'nullable|in:' . implode(',', self::RESPONSE),
            'challenge'                     => 'nullable|in:' . implode(',', self::CHALLENGE),
            'challenge_other'               => 'nullable|string|max:255|required_if:challenge,lainnya',
            'physical_condition_other'      => 'nullable|string|max:255|required_if:physical_condition_arrival,lainnya',
            'physical_condition_end_other'  => 'nullable|string|max:255|required_if:physical_condition_end,lainnya',
            'physical_energy_arrival_other' => 'nullable|string|max:255|required_if:physical_energy_arrival,lainnya',
            'physical_energy_end_other'     => 'nullable|string|max:255|required_if:physical_energy_end,lainnya',
            'independence_other'            => 'nullable|string|max:255|required_if:independence,lainnya',
            'behavior_other'                => 'nullable|string|max:255|required_if:behavior,lainnya',
            'response_other'                => 'nullable|string|max:255|required_if:response,lainnya',
            'solution_notes'                => 'nullable|string',
            'has_homework'                  => 'nullable|boolean',
            'homework_detail'               => 'nullable|string',
            'achievement_note'              => 'nullable|string|max:500',
            'achievement_tag'               => 'nullable|in:' . implode(',', self::ACHIEVEMENT_TAG),
            'communication_mode'            => 'nullable|in:' . implode(',', self::COMMUNICATION_MODE),
            'communication_initiative'      => 'nullable|in:' . implode(',', self::COMMUNICATION_INITIATIVE),
            'social_with_teacher'           => 'nullable|in:' . implode(',', self::SOCIAL_WITH_TEACHER),
            'social_with_peers'             => 'nullable|in:' . implode(',', self::SOCIAL_WITH_PEERS),
            'photo_physical'                => 'nullable|array|max:3',
            'photo_physical.*'              => 'file|max:51200',
            'photo_activity'                => 'nullable|array|max:3',
            'photo_activity.*'              => 'file|max:51200',
            'photo_other'                   => 'nullable|array|max:3',
            'photo_other.*'                 => 'file|max:51200',
        ]);

        $attendanceStatus = $request->input('attendance_status', $report->attendance_status ?? 'hadir');
        $isAbsent         = $attendanceStatus !== 'hadir';

        $report->update([
            'attendance_status' => $attendanceStatus,
        ]);

        if ($isAbsent) {
            return response()->json([
                'message' => 'Laporan berhasil diupdate (absen).',
                'report'  => $report->load([
                    'detail',
                    'classification',
                    'student:id,name',
                    'shadowTeacher:id,name,role',
                    'therapist:id,name,role',
                ]),
            ]);
        }

        $detail     = $report->detail;
        $updateData = $request->only([
            'physical_condition_arrival', 'physical_condition_end',
            'physical_energy_arrival', 'physical_energy_end',
            'independence', 'mood_arrival', 'mood_end', 'behavior',
            'activity_notes', 'response', 'challenge',
            'solution_notes', 'has_homework', 'homework_detail',
            'achievement_note', 'achievement_tag',
            'communication_mode', 'communication_initiative',
            'social_with_teacher', 'social_with_peers',
        ]);

        $otherFields = [
            'physical_condition_arrival' => 'physical_condition_other',
            'physical_condition_end'     => 'physical_condition_end_other',
            'physical_energy_arrival'    => 'physical_energy_arrival_other',
            'physical_energy_end'        => 'physical_energy_end_other',
            'independence'               => 'independence_other',
            'behavior'                   => 'behavior_other',
            'response'                   => 'response_other',
            'challenge'                  => 'challenge_other',
        ];

        foreach ($otherFields as $enumField => $otherField) {
            $enumValue = $request->input($enumField, $detail->$enumField);
            $updateData[$otherField] = $enumValue === 'lainnya'
                ? $request->input($otherField)
                : null;
        }

        $photoFields = [
            'photo_physical' => 'physical',
            'photo_activity' => 'activity',
            'photo_other'    => 'other',
        ];

        foreach ($photoFields as $field => $folder) {
            if ($request->hasFile($field)) {
                $this->deleteMultipleFromCloudinary($detail->$field);
                $updateData[$field] = $this->uploadMultiple($request->file($field), $folder);
            }
        }

        $textFields = collect([
            $updateData['activity_notes']   ?? $detail->activity_notes,
            $updateData['solution_notes']   ?? $detail->solution_notes,
            $updateData['homework_detail']  ?? $detail->homework_detail,
            $updateData['achievement_note'] ?? $detail->achievement_note,
        ])->filter()->implode(' ');

        $updateData['text_length'] = str_word_count($textFields);

        $detail->update($updateData);

        $report->load('detail');
        app(\App\Services\ReportClassificationService::class)->classify($report);

        return response()->json([
            'message' => 'Laporan berhasil diupdate.',
            'report'  => $report->load([
                'detail',
                'classification',
                'student:id,name',
                'shadowTeacher:id,name,role',
                'therapist:id,name,role',
            ]),
        ]);
    }

    // DELETE /api/daily-reports/{id}
    public function destroy($id)
    {
        $report = DailyReport::with('detail')->findOrFail($id);

        if ($report->detail) {
            $this->deleteMultipleFromCloudinary($report->detail->photo_physical);
            $this->deleteMultipleFromCloudinary($report->detail->photo_activity);
            $this->deleteMultipleFromCloudinary($report->detail->photo_other);
        }

        $report->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus.']);
    }

    // GET /api/daily-reports/form-options
    public function formOptions()
    {
        return response()->json([
            // Field lama
            'physical_condition_arrival' => self::PHYSICAL_CONDITION,
            'physical_condition_end'     => self::PHYSICAL_CONDITION_END,
            'physical_energy_arrival'    => self::PHYSICAL_ENERGY,
            'physical_energy_end'        => self::PHYSICAL_ENERGY,
            'independence'               => self::INDEPENDENCE,
            'behavior'                   => self::BEHAVIOR,
            'response'                   => self::RESPONSE,
            'challenge'                  => self::CHALLENGE,
            'mood_scale'                 => [1, 2, 3, 4, 5],
            // Field baru — key sesuai format ketua
            'attendance_options'                 => self::ATTENDANCE_STATUS,
            'achievement_tag_options'            => self::ACHIEVEMENT_TAG,
            'communication_mode_options'         => self::COMMUNICATION_MODE,
            'communication_initiative_options'   => self::COMMUNICATION_INITIATIVE,
            'social_with_teacher_options'        => self::SOCIAL_WITH_TEACHER,
            'social_with_peers_options'          => self::SOCIAL_WITH_PEERS,
        ]);
    }

    // GET /api/daily-reports/my-students
    public function myStudents(Request $request)
    {
        $user = $request->user();

        $classStudents = \App\Models\ClassRoom::where('homeroom_teacher_id', $user->id)
            ->with('students:id')
            ->get()
            ->flatMap(fn($c) => $c->students->pluck('id'));

        $shadowStudents = \App\Models\ShadowGroup::where('pic_id', $user->id)
            ->orWhere('partner_id', $user->id)
            ->pluck('student_id');

        $oneOnOneStudents = \App\Models\OneOnOneGroup::where('teacher_id', $user->id)
            ->pluck('student_id');

        $studentIds = $classStudents
            ->merge($shadowStudents)
            ->merge($oneOnOneStudents)
            ->unique()
            ->values();

        $students = Student::whereIn('id', $studentIds)
            ->with('classes:id,name')
            ->get()
            ->map(function ($s) {
                $todayReport = DailyReport::where('student_id', $s->id)
                    ->whereDate('date', today())
                    ->first();

                return [
                    'id'            => $s->id,
                    'name'          => $s->name,
                    'photo'         => $s->photo,
                    'class'         => $s->classes?->first()?->name,
                    'report_status' => $todayReport
                        ? ($todayReport->attendance_status !== 'hadir' ? 'absen' : 'sudah_lapor')
                        : 'belum_lapor',
                    'attendance_status' => $todayReport?->attendance_status,
                    'report_id'     => $todayReport?->id,
                ];
            });

        return response()->json([
            'success' => true,
            'total'   => $students->count(),
            'data'    => $students->values(),
        ]);
    }
}
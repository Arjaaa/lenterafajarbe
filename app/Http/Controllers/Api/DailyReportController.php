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
    const CHALLENGE              = ['kurang_fokus', 'mudah_terdistraksi', 'mood_kurang_stabil', 'sulit_diarahkan', 'lainnya'];
    const INDEPENDENCE           = ['mandiri', 'perlu_bantuan', 'sangat_mandiri', 'lainnya'];

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

    /**
     * Upload array of files ke Cloudinary, return array of URLs.
     * Max 3 file per section.
     */
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

    /**
     * Hapus semua URL dalam array dari Cloudinary.
     */
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
            // foto sekarang array, max 3 file per section
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

        $report = DailyReport::create([
            'student_id'        => $request->student_id,
            'shadow_teacher_id' => $shadowTeacherId,
            'therapist_id'      => $therapistId,
            'date'              => $request->date,
        ]);

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
            'photo_physical'                => $photoPhysical,
            'photo_activity'                => $photoActivity,
            'photo_other'                   => $photoOther,
            'text_length'                   => str_word_count($textFields),
        ]);

        $report->load('detail');
        app(\App\Services\ReportClassificationService::class)->classify($report);

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

    // POST /api/daily-reports/{id}
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
            // foto sekarang array, max 3 file per section
            'photo_physical'                => 'nullable|array|max:3',
            'photo_physical.*'              => 'file|max:51200',
            'photo_activity'                => 'nullable|array|max:3',
            'photo_activity.*'              => 'file|max:51200',
            'photo_other'                   => 'nullable|array|max:3',
            'photo_other.*'                 => 'file|max:51200',
        ]);

        $detail     = $report->detail;
        $updateData = $request->only([
            'physical_condition_arrival', 'physical_condition_end',
            'physical_energy_arrival', 'physical_energy_end',
            'independence', 'mood_arrival', 'mood_end', 'behavior',
            'activity_notes', 'response', 'challenge',
            'solution_notes', 'has_homework', 'homework_detail',
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

        // Handle update foto — hapus lama, upload baru
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
            $updateData['activity_notes'] ?? $detail->activity_notes,
            $updateData['solution_notes'] ?? $detail->solution_notes,
            $updateData['homework_detail'] ?? $detail->homework_detail,
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
            'physical_condition_arrival' => self::PHYSICAL_CONDITION,
            'physical_condition_end'     => self::PHYSICAL_CONDITION_END,
            'physical_energy_arrival'    => self::PHYSICAL_ENERGY,
            'physical_energy_end'        => self::PHYSICAL_ENERGY,
            'independence'               => self::INDEPENDENCE,
            'behavior'                   => self::BEHAVIOR,
            'response'                   => self::RESPONSE,
            'challenge'                  => self::CHALLENGE,
            'mood_scale'                 => [1, 2, 3, 4, 5],
        ]);
    }
}
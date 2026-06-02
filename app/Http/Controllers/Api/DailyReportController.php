<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Student;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    const PHYSICAL_CONDITION = ['sehat', 'sedikit_lelah', 'kurang_fit', 'mengantuk', 'lainnya'];
    const PHYSICAL_CONDITION_END = ['ceria', 'aktif', 'lelah', 'tenang', 'lainnya'];
    const BEHAVIOR           = ['kooperatif', 'fokus', 'aktif', 'mudah_terdistraksi', 'lainnya'];
    const RESPONSE           = ['antusias', 'pasif', 'perlu_arahan', 'perlu_pengawasan', 'lainnya'];
    const CHALLENGE          = ['kurang_fokus', 'mudah_terdistraksi', 'mood_kurang_stabil', 'sulit_diarahkan', 'lainnya'];

    private function uploadToCloudinary($file, string $folder): string
    {
        $mimeType = $file->getMimeType();
        $isVideo  = str_starts_with($mimeType, 'video/');

        if ($isVideo) {
            $videoService  = app(\App\Services\VideoCompressionService::class);
            $compressedPath = $videoService->compress($file->getRealPath());

            $uploaded = cloudinary()->upload($compressedPath, [
                'folder'        => 'guru-report/' . $folder,
                'resource_type' => 'video',
            ]);

            // Hapus file temp hasil compress
            if (file_exists($compressedPath)) {
                unlink($compressedPath);
            }
        } else {
            // Image — compress via Cloudinary transformation
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

    // ─── Hapus foto dari Cloudinary ───────────────────────────────────────────
    private function deleteFromCloudinary(?string $url): void
    {
        if (!$url) return;

        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z0-9]+$/i', $url, $matches);
        if (empty($matches[1])) return;

        $publicId = $matches[1];

        // Coba hapus sebagai image dulu, kalau gagal coba video
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

    // ─────────────────────────────────────────────────────────────────────────

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
            'student_id'                   => 'required|exists:students,id',
            'date'                         => 'required|date',
            'physical_condition_arrival'   => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION),
            'physical_condition_end'       => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION_END),
            'mood_arrival'                 => 'nullable|integer|min:1|max:5',
            'mood_end'                     => 'nullable|integer|min:1|max:5',
            'behavior'                     => 'nullable|in:' . implode(',', self::BEHAVIOR),
            'activity_notes'               => 'nullable|string',
            'response'                     => 'nullable|in:' . implode(',', self::RESPONSE),
            'challenge'                    => 'nullable|in:' . implode(',', self::CHALLENGE),
            'challenge_other'              => 'nullable|string|max:255|required_if:challenge,lainnya',
            'physical_condition_other'     => 'nullable|string|max:255|required_if:physical_condition_arrival,lainnya',
            'physical_condition_end_other' => 'nullable|string|max:255|required_if:physical_condition_end,lainnya',
            'behavior_other'               => 'nullable|string|max:255|required_if:behavior,lainnya',
            'response_other'               => 'nullable|string|max:255|required_if:response,lainnya',
            'solution_notes'               => 'nullable|string',
            'has_homework'                 => 'nullable|boolean',
            'homework_detail'              => 'nullable|string',
            // Semua jenis media (foto, video, dll) — max 50MB
            'photo_physical'               => 'nullable|file|max:51200',
            'photo_activity'               => 'nullable|file|max:51200',
            'photo_other'                  => 'nullable|file|max:51200',
        ]);

        // Cek laporan sudah ada
        $exists = DailyReport::where('student_id', $request->student_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Laporan untuk murid ini pada tanggal tersebut sudah ada.',
            ], 422);
        }

        // Tentukan siapa yang membuat laporan
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

        // Upload foto ke Cloudinary (auto compress)
        $photoPhysical = $request->hasFile('photo_physical')
            ? $this->uploadToCloudinary($request->file('photo_physical'), 'physical')
            : null;

        $photoActivity = $request->hasFile('photo_activity')
            ? $this->uploadToCloudinary($request->file('photo_activity'), 'activity')
            : null;

        $photoOther = $request->hasFile('photo_other')
            ? $this->uploadToCloudinary($request->file('photo_other'), 'other')
            : null;

        // Hitung text_length
        $textFields = collect([
            $request->activity_notes,
            $request->solution_notes,
            $request->homework_detail,
        ])->filter()->implode(' ');

        $report->detail()->create([
            'physical_condition_arrival'   => $request->physical_condition_arrival,
            'physical_condition_other'     => $request->physical_condition_arrival === 'lainnya' ? $request->physical_condition_other : null,
            'physical_condition_end'       => $request->physical_condition_end,
            'physical_condition_end_other' => $request->physical_condition_end === 'lainnya' ? $request->physical_condition_end_other : null,
            'mood_arrival'                 => $request->mood_arrival,
            'mood_end'                     => $request->mood_end,
            'behavior'                     => $request->behavior,
            'behavior_other'               => $request->behavior === 'lainnya' ? $request->behavior_other : null,
            'activity_notes'               => $request->activity_notes,
            'response'                     => $request->response,
            'response_other'               => $request->response === 'lainnya' ? $request->response_other : null,
            'challenge'                    => $request->challenge,
            'challenge_other'              => $request->challenge === 'lainnya' ? $request->challenge_other : null,
            'solution_notes'               => $request->solution_notes,
            'has_homework'                 => $request->has_homework ?? false,
            'homework_detail'              => $request->homework_detail,
            'photo_physical'               => $photoPhysical,
            'photo_activity'               => $photoActivity,
            'photo_other'                  => $photoOther,
            'text_length'                  => str_word_count($textFields),
        ]);

        // Auto klasifikasi setelah laporan disimpan
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
            'physical_condition_arrival'   => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION),
            'physical_condition_end'       => 'nullable|in:' . implode(',', self::PHYSICAL_CONDITION_END),
            'mood_arrival'                 => 'nullable|integer|min:1|max:5',
            'mood_end'                     => 'nullable|integer|min:1|max:5',
            'behavior'                     => 'nullable|in:' . implode(',', self::BEHAVIOR),
            'activity_notes'               => 'nullable|string',
            'response'                     => 'nullable|in:' . implode(',', self::RESPONSE),
            'challenge'                    => 'nullable|in:' . implode(',', self::CHALLENGE),
            'challenge_other'              => 'nullable|string|max:255|required_if:challenge,lainnya',
            'physical_condition_other'     => 'nullable|string|max:255|required_if:physical_condition_arrival,lainnya',
            'physical_condition_end_other' => 'nullable|string|max:255|required_if:physical_condition_end,lainnya',
            'behavior_other'               => 'nullable|string|max:255|required_if:behavior,lainnya',
            'response_other'               => 'nullable|string|max:255|required_if:response,lainnya',
            'solution_notes'               => 'nullable|string',
            'has_homework'                 => 'nullable|boolean',
            'homework_detail'              => 'nullable|string',
            'photo_physical'               => 'nullable|file|max:51200',
            'photo_activity'               => 'nullable|file|max:51200',
            'photo_other'                  => 'nullable|file|max:51200',
        ]);

        $detail     = $report->detail;
        $updateData = $request->only([
            'physical_condition_arrival', 'physical_condition_end',
            'mood_arrival', 'mood_end', 'behavior', 'activity_notes',
            'response', 'challenge', 'solution_notes', 'has_homework', 'homework_detail',
        ]);

        // Handle _other fields — hanya simpan jika pilihan adalah "lainnya"
        $otherFields = [
            'physical_condition_arrival' => 'physical_condition_other',
            'physical_condition_end'     => 'physical_condition_end_other',
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

        // Update foto jika ada yang baru
        $photoFields = [
            'photo_physical' => 'physical',
            'photo_activity' => 'activity',
            'photo_other'    => 'other',
        ];

        foreach ($photoFields as $field => $folder) {
            if ($request->hasFile($field)) {
                // Hapus foto lama dari Cloudinary
                $this->deleteFromCloudinary($detail->$field);
                // Upload foto baru
                $updateData[$field] = $this->uploadToCloudinary($request->file($field), $folder);
            }
        }

        // Recalculate text_length
        $textFields = collect([
            $updateData['activity_notes'] ?? $detail->activity_notes,
            $updateData['solution_notes'] ?? $detail->solution_notes,
            $updateData['homework_detail'] ?? $detail->homework_detail,
        ])->filter()->implode(' ');

        $updateData['text_length'] = str_word_count($textFields);

        $detail->update($updateData);

        // Re-klasifikasi setelah update
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

        // Hapus semua foto dari Cloudinary
        if ($report->detail) {
            $this->deleteFromCloudinary($report->detail->photo_physical);
            $this->deleteFromCloudinary($report->detail->photo_activity);
            $this->deleteFromCloudinary($report->detail->photo_other);
        }

        $report->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus.']);
    }

    // GET /api/daily-reports/form-options
    public function formOptions()
    {
        return response()->json([
            'physical_condition_arrival' => self::PHYSICAL_CONDITION,
            'physical_condition_end'     => self::PHYSICAL_CONDITION,
            'behavior'                   => self::BEHAVIOR,
            'response'                   => self::RESPONSE,
            'challenge'                  => self::CHALLENGE,
            'mood_scale'                 => [1, 2, 3, 4, 5],
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentDocumentation;
use App\Models\Student;
use App\Services\VideoCompressionService;
use Illuminate\Http\Request;

class StudentDocumentationController extends Controller
{
    // ─── Upload ke Cloudinary ──────────────────────────────────────────────────

    private function uploadMedia($file, string $folder, string $type): array
    {
        if ($type === 'video') {
            $videoService   = app(VideoCompressionService::class);
            $compressedPath = $videoService->compress($file->getRealPath());

            $uploaded = cloudinary()->upload($compressedPath, [
                'folder'        => 'guru-report/' . $folder,
                'resource_type' => 'video',
            ]);

            if (file_exists($compressedPath)) {
                unlink($compressedPath);
            }

            $videoUrl     = $uploaded->getSecurePath();
            $thumbnailUrl = $this->generateVideoThumbnail($videoUrl);

            return [
                'media_url'     => $videoUrl,
                'thumbnail_url' => $thumbnailUrl,
            ];
        }

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

        return [
            'media_url'     => $uploaded->getSecurePath(),
            'thumbnail_url' => null,
        ];
    }

    // ─── Upload multiple files, return array of URLs ───────────────────────────

    private function uploadMultipleMedia(array $files, string $folder, string $type): array
    {
        $mediaUrls     = [];
        $thumbnailUrls = [];

        foreach (array_slice($files, 0, 3) as $file) {
            $uploaded        = $this->uploadMedia($file, $folder, $type);
            $mediaUrls[]     = $uploaded['media_url'];
            $thumbnailUrls[] = $uploaded['thumbnail_url'];
        }

        return [
            'media_urls'     => $mediaUrls,
            'thumbnail_urls' => $thumbnailUrls,
        ];
    }

    // ─── Generate thumbnail URL dari video Cloudinary ─────────────────────────

    private function generateVideoThumbnail(string $videoUrl): string
    {
        return preg_replace(
            '/\/upload\//',
            '/upload/so_0,w_400,h_400,c_fill,f_jpg/',
            $videoUrl
        );
    }

    // ─── Hapus dari Cloudinary ────────────────────────────────────────────────

    private function deleteFromCloudinary(?string $url, string $resourceType = 'image'): void
    {
        if (!$url) return;

        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z0-9]+$/i', $url, $matches);
        if (empty($matches[1])) return;

        try {
            cloudinary()->destroy($matches[1], ['resource_type' => $resourceType]);
        } catch (\Exception $e) {
            // Abaikan jika gagal hapus
        }
    }

    private function deleteMultipleFromCloudinary(?array $urls, string $resourceType = 'image'): void
    {
        if (empty($urls)) return;
        foreach ($urls as $url) {
            $this->deleteFromCloudinary($url, $resourceType);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    // GET /api/students/{studentId}/documentations
    public function index(Request $request, $studentId)
    {
        Student::findOrFail($studentId);

        $query = StudentDocumentation::with('uploader:id,name,role')
            ->where('student_id', $studentId)
            ->latest('activity_date');

        if ($request->has('media_type')) {
            $query->where('media_type', $request->media_type);
        }

        if ($request->has('date')) {
            $query->whereDate('activity_date', $request->date);
        }

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('activity_date', $request->month)
                  ->whereYear('activity_date', $request->year);
        }

        $docs = $query->get()->map(fn($d) => $this->formatDoc($d));

        $totalPhoto = StudentDocumentation::where('student_id', $studentId)
            ->where('media_type', 'photo')->count();

        $totalVideo = StudentDocumentation::where('student_id', $studentId)
            ->where('media_type', 'video')->count();

        $todayCount = StudentDocumentation::where('student_id', $studentId)
            ->whereDate('activity_date', today())->count();

        $todayActivities = StudentDocumentation::where('student_id', $studentId)
            ->whereDate('activity_date', today())
            ->distinct('title')->count('title');

        return response()->json([
            'success' => true,
            'stats'   => [
                'total_photo'    => $totalPhoto,
                'total_video'    => $totalVideo,
                'doc_today'      => $todayCount,
                'activity_today' => $todayActivities,
            ],
            'data' => $docs,
        ]);
    }

    // GET /api/students/{studentId}/documentations/{id}
    public function show($studentId, $id)
    {
        $doc = StudentDocumentation::with('uploader:id,name,role')
            ->where('student_id', $studentId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->formatDoc($doc),
        ]);
    }

    // POST /api/students/{studentId}/documentations
    public function store(Request $request, $studentId)
    {
        Student::findOrFail($studentId);

        $request->validate([
            'media_type'    => 'required|in:photo,video',
            'media'         => 'required|array|max:3',
            'media.*'       => 'file|max:102400',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'activity_date' => 'required|date',
        ]);

        $type = $request->media_type;

        // Video maksimal 1 file
        if ($type === 'video' && count($request->file('media')) > 1) {
            return response()->json([
                'message' => 'Upload video maksimal 1 file.',
            ], 422);
        }

        $folder   = $type === 'video' ? 'documentation/videos' : 'documentation/photos';
        $uploaded = $this->uploadMultipleMedia($request->file('media'), $folder, $type);

        $doc = StudentDocumentation::create([
            'student_id'    => $studentId,
            'uploaded_by'   => $request->user()->id,
            'media_type'    => $type,
            'media_url'     => $uploaded['media_urls'],
            'thumbnail_url' => $uploaded['thumbnail_urls'],
            'title'         => $request->title,
            'description'   => $request->description,
            'activity_date' => $request->activity_date,
        ]);

        $doc->load('uploader:id,name,role');

        return response()->json([
            'success' => true,
            'message' => 'Dokumentasi berhasil diupload.',
            'data'    => $this->formatDoc($doc),
        ], 201);
    }

    // PUT /api/students/{studentId}/documentations/{id}
    public function update(Request $request, $studentId, $id)
    {
        $doc = StudentDocumentation::where('student_id', $studentId)->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($doc->uploaded_by !== $user->id && !$user->isCoordinator()) {
            return response()->json(['message' => 'Anda tidak berhak mengedit dokumentasi ini.'], 403);
        }

        $request->validate([
            'media'         => 'nullable|array|max:3',
            'media.*'       => 'file|max:102400',
            'title'         => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'activity_date' => 'nullable|date',
        ]);

        // Video maksimal 1 file
        if ($request->hasFile('media') && $doc->media_type === 'video' && count($request->file('media')) > 1) {
            return response()->json([
                'message' => 'Upload video maksimal 1 file.',
            ], 422);
        }

        $updateData = $request->only(['title', 'description', 'activity_date']);

        if ($request->hasFile('media')) {
            $resourceType = $doc->media_type === 'video' ? 'video' : 'image';
            $this->deleteMultipleFromCloudinary($doc->media_url, $resourceType);
            $this->deleteMultipleFromCloudinary(array_filter($doc->thumbnail_url ?? []), 'image');

            $folder   = $doc->media_type === 'video' ? 'documentation/videos' : 'documentation/photos';
            $uploaded = $this->uploadMultipleMedia($request->file('media'), $folder, $doc->media_type);

            $updateData['media_url']     = $uploaded['media_urls'];
            $updateData['thumbnail_url'] = $uploaded['thumbnail_urls'];
        }

        $doc->update($updateData);
        $doc->load('uploader:id,name,role');

        return response()->json([
            'success' => true,
            'message' => 'Dokumentasi berhasil diperbarui.',
            'data'    => $this->formatDoc($doc),
        ]);
    }

    // DELETE /api/students/{studentId}/documentations/{id}
    public function destroy(Request $request, $studentId, $id)
    {
        $doc = StudentDocumentation::where('student_id', $studentId)->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($doc->uploaded_by !== $user->id && !$user->isCoordinator()) {
            return response()->json(['message' => 'Anda tidak berhak menghapus dokumentasi ini.'], 403);
        }

        $resourceType = $doc->media_type === 'video' ? 'video' : 'image';
        $this->deleteMultipleFromCloudinary($doc->media_url, $resourceType);
        $this->deleteMultipleFromCloudinary(array_filter($doc->thumbnail_url ?? []), 'image');

        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumentasi berhasil dihapus.',
        ]);
    }

    // ─── Format response ──────────────────────────────────────────────────────

    private function formatDoc(StudentDocumentation $doc): array
    {
        $mediaUrls     = $doc->media_url     ?? [];
        $thumbnailUrls = $doc->thumbnail_url ?? [];

        $thumbnails = array_map(
            fn($thumb, $media) => $thumb ?? $media,
            $thumbnailUrls,
            $mediaUrls
        );

        return [
            'id'             => $doc->id,
            'student_id'     => $doc->student_id,
            'media_type'     => $doc->media_type,
            'media_urls'     => $mediaUrls,
            'thumbnail_urls' => $thumbnails,
            'title'          => $doc->title,
            'description'    => $doc->description,
            'activity_date'  => $doc->activity_date?->toDateString(),
            'uploaded_by'    => [
                'id'   => $doc->uploader?->id,
                'name' => $doc->uploader?->name,
                'role' => $doc->uploader?->role,
            ],
            'created_at' => $doc->created_at,
        ];
    }
}
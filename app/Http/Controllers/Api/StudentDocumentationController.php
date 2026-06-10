<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentDocumentation;
use App\Models\Student;
use App\Services\VideoCompressionService;
use Illuminate\Http\Request;

class StudentDocumentationController extends Controller
{
    private function uploadMedia($file): array
    {
        $mimeType = $file->getMimeType();
        $isVideo  = str_starts_with($mimeType, 'video/');

        if ($isVideo) {
            $videoService   = app(VideoCompressionService::class);
            $compressedPath = $videoService->compress($file->getRealPath());

            $uploaded = cloudinary()->upload($compressedPath, [
                'folder'        => 'guru-report/documentation/videos',
                'resource_type' => 'video',
            ]);

            if (file_exists($compressedPath)) unlink($compressedPath);

            $videoUrl     = $uploaded->getSecurePath();
            $thumbnailUrl = $this->generateVideoThumbnail($videoUrl);

            return ['media_url' => $videoUrl, 'thumbnail_url' => $thumbnailUrl, 'media_type' => 'video'];
        }

        $uploaded = cloudinary()->upload($file->getRealPath(), [
            'folder'         => 'guru-report/documentation/photos',
            'resource_type'  => 'image',
            'transformation' => [
                'quality'      => 'auto',
                'fetch_format' => 'auto',
                'width'        => 1200,
                'crop'         => 'limit',
            ],
        ]);

        return ['media_url' => $uploaded->getSecurePath(), 'thumbnail_url' => null, 'media_type' => 'photo'];
    }

    private function uploadMultipleMedia(array $files): array
    {
        $mediaUrls = $thumbnailUrls = $mediaTypes = [];
        foreach (array_slice($files, 0, 3) as $file) {
            $u = $this->uploadMedia($file);
            $mediaUrls[]     = $u['media_url'];
            $thumbnailUrls[] = $u['thumbnail_url'];
            $mediaTypes[]    = $u['media_type'];
        }
        return ['media_urls' => $mediaUrls, 'thumbnail_urls' => $thumbnailUrls, 'media_types' => $mediaTypes];
    }

    private function generateVideoThumbnail(string $url): string
    {
        return preg_replace('/\/upload\//', '/upload/so_0,w_400,h_400,c_fill,f_jpg/', $url);
    }

    private function deleteFromCloudinary(?string $url, string $resourceType = 'image'): void
    {
        if (!$url) return;
        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z0-9]+$/i', $url, $matches);
        if (empty($matches[1])) return;
        try { cloudinary()->destroy($matches[1], ['resource_type' => $resourceType]); } catch (\Exception $e) {}
    }

    private function deleteMultipleFromCloudinary(?array $urls, ?array $mediaTypes = null): void
    {
        if (empty($urls)) return;
        foreach ($urls as $i => $url) {
            $type = $mediaTypes[$i] ?? 'photo';
            $this->deleteFromCloudinary($url, $type === 'video' ? 'video' : 'image');
        }
    }

    private function detectTypeFromUrl(string $url): string
    {
        return preg_match('/\.(mp4|mov|avi|mkv|webm)/i', $url) ? 'video' : 'photo';
    }

    private function calcStats($docs, $studentId): array
    {
        $totalPhoto = $docs->sum(function ($d) {
            $types = $d->media_types ?? [];
            if (!empty($types)) return collect($types)->filter(fn($t) => $t === 'photo')->count();
            return collect($d->media_url ?? [])->filter(fn($u) => preg_match('/\.(jpg|jpeg|png|gif|webp)/i', $u))->count();
        });

        $totalVideo = $docs->sum(function ($d) {
            $types = $d->media_types ?? [];
            if (!empty($types)) return collect($types)->filter(fn($t) => $t === 'video')->count();
            return collect($d->media_url ?? [])->filter(fn($u) => preg_match('/\.(mp4|mov|avi|mkv|webm)/i', $u))->count();
        });

        $todayCount = StudentDocumentation::where('student_id', $studentId)
            ->whereDate('activity_date', today())->count();

        $todayActivities = StudentDocumentation::where('student_id', $studentId)
            ->whereDate('activity_date', today())
            ->distinct('title')->count('title');

        return [
            'total_photo'    => $totalPhoto,
            'total_video'    => $totalVideo,
            'doc_today'      => $todayCount,
            'activity_today' => $todayActivities,
        ];
    }

    private function formatDoc(StudentDocumentation $doc): array
    {
        $mediaUrls     = $doc->media_url     ?? [];
        $thumbnailUrls = $doc->thumbnail_url ?? [];
        $mediaTypes    = $doc->media_types   ?? [];

        if (empty($mediaTypes) && !empty($mediaUrls)) {
            $mediaTypes = array_map(fn($u) => $this->detectTypeFromUrl($u), $mediaUrls);
        }

        $thumbnails = array_map(fn($t, $m) => $t ?? $m, $thumbnailUrls, $mediaUrls);

        return [
            'id'             => $doc->id,
            'student_id'     => $doc->student_id,
            'media_urls'     => $mediaUrls,
            'thumbnail_urls' => $thumbnails,
            'media_types'    => $mediaTypes,
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

    // GET /api/students/{studentId}/documentations
    public function index(Request $request, $studentId)
    {
        Student::findOrFail($studentId);

        $query = StudentDocumentation::with(['uploader:id,name,role'])
            ->where('student_id', $studentId)
            ->latest('activity_date');

        if ($request->has('date')) {
            $query->whereDate('activity_date', $request->date);
        }

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('activity_date', $request->month)
                  ->whereYear('activity_date', $request->year);
        }

        $allDocs   = $query->get();
        $stats     = $this->calcStats($allDocs, $studentId);
        $paginated = $query->cursorPaginate(5);

        return response()->json([
            'success' => true,
            'stats'   => $stats,
            'data'    => collect($paginated->items())->map(fn($d) => $this->formatDoc($d))->values(),
            'meta'    => [
                'next_cursor' => $paginated->nextCursor()?->encode(),
                'per_page'    => $paginated->perPage(),
                'has_more'    => $paginated->hasMorePages(),
            ],
        ]);
    }

    // GET /api/students/{studentId}/documentations/{id}
    public function show($studentId, $id)
    {
        Student::findOrFail($studentId);

        $doc = StudentDocumentation::with(['uploader:id,name,role'])
            ->where('student_id', $studentId)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $this->formatDoc($doc)]);
    }

    // POST /api/students/{studentId}/documentations/upload
    public function store(Request $request, $studentId)
    {
        Student::findOrFail($studentId);

        if ($request->user()->role !== 'therapist_homeroom') {
            return response()->json(['message' => 'Hanya therapist homeroom yang bisa upload dokumentasi.'], 403);
        }

        $request->validate([
            'media'         => 'required|array|max:3',
            'media.*'       => 'file|max:102400',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'activity_date' => 'required|date',
        ]);

        $uploaded = $this->uploadMultipleMedia($request->file('media'));

        $doc = StudentDocumentation::create([
            'student_id'    => $studentId,
            'uploaded_by'   => $request->user()->id,
            'media_url'     => $uploaded['media_urls'],
            'thumbnail_url' => $uploaded['thumbnail_urls'],
            'media_types'   => $uploaded['media_types'],
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
        Student::findOrFail($studentId);
        $doc  = StudentDocumentation::where('student_id', $studentId)->findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'therapist_homeroom' && !$user->isCoordinator()) {
            return response()->json(['message' => 'Hanya therapist homeroom yang bisa mengedit dokumentasi.'], 403);
        }

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

        $updateData = $request->only(['title', 'description', 'activity_date']);

        if ($request->hasFile('media')) {
            $this->deleteMultipleFromCloudinary($doc->media_url, $doc->media_types);
            $uploaded = $this->uploadMultipleMedia($request->file('media'));
            $updateData['media_url']     = $uploaded['media_urls'];
            $updateData['thumbnail_url'] = $uploaded['thumbnail_urls'];
            $updateData['media_types']   = $uploaded['media_types'];
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
        Student::findOrFail($studentId);
        $doc  = StudentDocumentation::where('student_id', $studentId)->findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'therapist_homeroom' && !$user->isCoordinator()) {
            return response()->json(['message' => 'Hanya therapist homeroom yang bisa menghapus dokumentasi.'], 403);
        }

        if ($doc->uploaded_by !== $user->id && !$user->isCoordinator()) {
            return response()->json(['message' => 'Anda tidak berhak menghapus dokumentasi ini.'], 403);
        }

        $this->deleteMultipleFromCloudinary($doc->media_url, $doc->media_types);
        $doc->delete();

        return response()->json(['success' => true, 'message' => 'Dokumentasi berhasil dihapus.']);
    }
}
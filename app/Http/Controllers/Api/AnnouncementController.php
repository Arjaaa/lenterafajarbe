<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // ─── Upload ke Cloudinary ──────────────────────────────────────────────────

    private function uploadToCloudinary($file): string
    {
        $mimeType = $file->getMimeType();
        $isVideo  = str_starts_with($mimeType, 'video/');

        if ($isVideo) {
            $videoService   = app(\App\Services\VideoCompressionService::class);
            $compressedPath = $videoService->compress($file->getRealPath());

            $uploaded = cloudinary()->upload($compressedPath, [
                'folder'        => 'guru-report/announcements',
                'resource_type' => 'video',
            ]);

            if (file_exists($compressedPath)) {
                unlink($compressedPath);
            }
        } else {
            $uploaded = cloudinary()->upload($file->getRealPath(), [
                'folder'         => 'guru-report/announcements',
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

        try {
            cloudinary()->destroy($matches[1], ['resource_type' => 'image']);
        } catch (\Exception $e) {
            try {
                cloudinary()->destroy($matches[1], ['resource_type' => 'video']);
            } catch (\Exception $e) {
                // Abaikan
            }
        }
    }

    private function uploadMultiple(array $files): array
    {
        $urls = [];
        foreach (array_slice($files, 0, 5) as $file) {
            $urls[] = $this->uploadToCloudinary($file);
        }
        return $urls;
    }

    private function deleteMultiple(?array $urls): void
    {
        if (empty($urls)) return;
        foreach ($urls as $url) {
            $this->deleteFromCloudinary($url);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    // GET /api/announcements
    public function index()
    {
        $announcements = Announcement::with('creator:id,name')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $announcements,
        ]);
    }

    // GET /api/announcements/{id}
    public function show($id)
    {
        $announcement = Announcement::with('creator:id,name')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $announcement,
        ]);
    }

    // POST /api/announcements
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'type'        => 'nullable|in:info,warning,urgent',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'is_active'   => 'nullable|boolean',
            'media'       => 'nullable|array|max:5',
            'media.*'     => 'file|max:102400',
        ]);

        $mediaUrls = [];
        if ($request->hasFile('media')) {
            $mediaUrls = $this->uploadMultiple($request->file('media'));
        }

        $announcement = Announcement::create([
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type ?? 'info',
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_active'   => $request->is_active ?? true,
            'created_by'  => $request->user()->id,
            'media_urls'  => $mediaUrls,
        ]);

        $announcement->load('creator:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat.',
            'data'    => $announcement,
        ], 201);
    }

    // PUT /api/announcements/{id}
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type'        => 'sometimes|in:info,warning,urgent',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'is_active'   => 'sometimes|boolean',
            'media'       => 'nullable|array|max:5',
            'media.*'     => 'file|max:102400',
        ]);

        $updateData = $request->only([
            'title', 'description', 'type',
            'start_date', 'end_date', 'is_active',
        ]);

        if ($request->hasFile('media')) {
            $this->deleteMultiple($announcement->media_urls);
            $updateData['media_urls'] = $this->uploadMultiple($request->file('media'));
        }

        $announcement->update($updateData);
        $announcement->load('creator:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diupdate.',
            'data'    => $announcement,
        ]);
    }

    // DELETE /api/announcements/{id}
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->deleteMultiple($announcement->media_urls);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus.',
        ]);
    }
}
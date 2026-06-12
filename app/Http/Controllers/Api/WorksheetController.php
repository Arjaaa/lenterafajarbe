<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class WorksheetController extends Controller
{
    private function detectFileType($file): string
    {
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'video/')) return 'video';

        return 'unsupported';
    }

    private function uploadFile($file, string $fileType): string
    {
        if ($fileType === 'video') {
            $videoService   = app(\App\Services\VideoCompressionService::class);
            $compressedPath = $videoService->compress($file->getRealPath());

            $uploaded = cloudinary()->upload($compressedPath, [
                'folder'        => 'guru-report/worksheets',
                'resource_type' => 'video',
            ]);

            if (file_exists($compressedPath)) unlink($compressedPath);

            return $uploaded->getSecurePath();
        }

        // image — compress via Cloudinary
        $uploaded = cloudinary()->upload($file->getRealPath(), [
            'folder'         => 'guru-report/worksheets',
            'resource_type'  => 'image',
            'transformation' => [
                'quality'      => 'auto',
                'fetch_format' => 'auto',
                'width'        => 1200,
                'crop'         => 'limit',
            ],
        ]);

        return $uploaded->getSecurePath();
    }

    private function deleteFromCloudinary(?string $url, string $fileType): void
    {
        if (!$url) return;
        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z0-9]+$/i', $url, $matches);
        if (empty($matches[1])) return;

        $resourceType = $fileType === 'video' ? 'video' : 'image';

        try {
            cloudinary()->destroy($matches[1], ['resource_type' => $resourceType]);
        } catch (\Exception $e) {}
    }

    private function formatWorksheet(Worksheet $ws): array
    {
        return [
            'id'                => $ws->id,
            'title'             => $ws->title,
            'description'       => $ws->description,
            'file_url'          => $ws->file_url,
            'file_type'         => $ws->file_type,
            'original_filename' => $ws->original_filename,
            'status'            => $ws->status,
            'student'           => $ws->student ? [
                'id'    => $ws->student->id,
                'name'  => $ws->student->name,
                'class' => $ws->student->classes->first()?->name,
            ] : null,
            'uploaded_by' => [
                'id'   => $ws->uploader?->id,
                'name' => $ws->uploader?->name,
                'role' => $ws->uploader?->role,
            ],
            'created_at' => $ws->created_at,
            'updated_at' => $ws->updated_at,
        ];
    }

    // GET /api/worksheets/summary
    public function summary(Request $request)
    {
        $user  = $request->user();
        $query = Worksheet::with(['uploader:id,name,role', 'student:id,name', 'student.classes:id,name']);

        if (!$user->isCoordinator()) {
            $query->where('uploaded_by', $user->id);
        }

        $all = $query->get();

        $total     = $all->count();
        $submitted = $all->where('status', 'submitted')->count();
        $draft     = $all->where('status', 'draft')->count();

        $latest = $query->latest()->take(5)->get()
            ->map(fn($ws) => $this->formatWorksheet($ws))
            ->values();

        return response()->json([
            'success' => true,
            'stats'   => [
                'total'     => $total,
                'submitted' => $submitted,
                'draft'     => $draft,
            ],
            'latest' => $latest,
        ]);
    }

    // GET /api/worksheets
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Worksheet::with(['uploader:id,name,role', 'student:id,name', 'student.classes:id,name'])
            ->latest();

        if (!$user->isCoordinator()) {
            $query->where('uploaded_by', $user->id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', $request->year);
        }

        $worksheets = $query->get();

        return response()->json([
            'success' => true,
            'stats'   => [
                'total'     => $worksheets->count(),
                'submitted' => $worksheets->where('status', 'submitted')->count(),
                'draft'     => $worksheets->where('status', 'draft')->count(),
            ],
            'data' => $worksheets->map(fn($ws) => $this->formatWorksheet($ws))->values(),
        ]);
    }

    // GET /api/worksheets/{id}
    public function show(Request $request, $id)
    {
        $user      = $request->user();
        $worksheet = Worksheet::with(['uploader:id,name,role', 'student:id,name', 'student.classes:id,name'])->findOrFail($id);

        if (!$user->isCoordinator() && $worksheet->uploaded_by !== $user->id) {
            return response()->json(['message' => 'Anda tidak berhak melihat worksheet ini.'], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatWorksheet($worksheet),
        ]);
    }

    // POST /api/worksheets/upload
    public function store(Request $request)
    {
        $request->validate([
            'file'        => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi,mkv,webm|max:51200',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'student_id'  => 'nullable|exists:students,id',
        ]);

        $file     = $request->file('file');
        $fileType = $this->detectFileType($file);

        if ($fileType === 'unsupported') {
            return response()->json([
                'message' => 'Hanya foto dan video yang diperbolehkan.',
            ], 422);
        }

        $fileUrl = $this->uploadFile($file, $fileType);

        $worksheet = Worksheet::create([
            'uploaded_by'       => $request->user()->id,
            'student_id'        => $request->student_id,
            'title'             => $request->title,
            'description'       => $request->description,
            'file_url'          => $fileUrl,
            'file_type'         => $fileType,
            'original_filename' => $file->getClientOriginalName(),
            'status'            => 'submitted',
        ]);

        $worksheet->load(['uploader:id,name,role', 'student:id,name', 'student.classes:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Worksheet berhasil diupload.',
            'data'    => $this->formatWorksheet($worksheet),
        ], 201);
    }

    // PUT /api/worksheets/{id}
    public function update(Request $request, $id)
    {
        $worksheet = Worksheet::findOrFail($id);
        $user      = $request->user();

        if (!$user->isCoordinator() && $worksheet->uploaded_by !== $user->id) {
            return response()->json(['message' => 'Anda tidak berhak mengedit worksheet ini.'], 403);
        }

        $request->validate([
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi,mkv,webm|max:51200',
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'student_id'  => 'nullable|exists:students,id',
        ]);

        $updateData = $request->only(['title', 'description', 'student_id']);

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $fileType = $this->detectFileType($file);

            if ($fileType === 'unsupported') {
                return response()->json([
                    'message' => 'Hanya foto dan video yang diperbolehkan.',
                ], 422);
            }

            $this->deleteFromCloudinary($worksheet->file_url, $worksheet->file_type);

            $updateData['file_url']          = $this->uploadFile($file, $fileType);
            $updateData['file_type']         = $fileType;
            $updateData['original_filename'] = $file->getClientOriginalName();
        }

        $worksheet->update($updateData);
        $worksheet->load(['uploader:id,name,role', 'student:id,name', 'student.classes:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Worksheet berhasil diupdate.',
            'data'    => $this->formatWorksheet($worksheet),
        ]);
    }

    // DELETE /api/worksheets/{id}
    public function destroy(Request $request, $id)
    {
        $worksheet = Worksheet::findOrFail($id);
        $user      = $request->user();

        if (!$user->isCoordinator() && $worksheet->uploaded_by !== $user->id) {
            return response()->json(['message' => 'Anda tidak berhak menghapus worksheet ini.'], 403);
        }

        $this->deleteFromCloudinary($worksheet->file_url, $worksheet->file_type);
        $worksheet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Worksheet berhasil dihapus.',
        ]);
    }
}
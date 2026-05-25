<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
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
        ]);

        $announcement = Announcement::create([
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type ?? 'info',
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_active'   => $request->is_active ?? true,
            'created_by'  => $request->user()->id,
        ]);

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
        ]);

        $announcement->update($request->only([
            'title', 'description', 'type',
            'start_date', 'end_date', 'is_active',
        ]));

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
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus.',
        ]);
    }
}
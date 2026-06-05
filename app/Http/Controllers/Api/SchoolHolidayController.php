<?php

namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\SchoolHoliday;
use Illuminate\Http\Request;
 
class SchoolHolidayController extends Controller
{
    // GET /api/school-holidays
    public function index(Request $request)
    {
        $query = SchoolHoliday::with('creator:id,name')->orderBy('date');
 
        if ($request->has('year')) {
            $query->whereYear('date', $request->year);
        }
 
        if ($request->has('month')) {
            $query->whereMonth('date', $request->month);
        }
 
        return response()->json([
            'success' => true,
            'data'    => $query->get(),
        ]);
    }
 
    // POST /api/school-holidays
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:school_holidays,date',
            'name' => 'required|string|max:150',
            'type' => 'required|in:nasional,sekolah',
        ]);
 
        $holiday = SchoolHoliday::create([
            'date'       => $request->date,
            'name'       => $request->name,
            'type'       => $request->type,
            'created_by' => $request->user()->id,
        ]);
 
        return response()->json([
            'success' => true,
            'message' => 'Hari libur berhasil ditambahkan.',
            'data'    => $holiday,
        ], 201);
    }
 
    // PUT /api/school-holidays/{id}
    public function update(Request $request, $id)
    {
        $holiday = SchoolHoliday::findOrFail($id);
 
        $request->validate([
            'date' => 'sometimes|date|unique:school_holidays,date,' . $id,
            'name' => 'sometimes|string|max:150',
            'type' => 'sometimes|in:nasional,sekolah',
        ]);
 
        $holiday->update($request->only('date', 'name', 'type'));
 
        return response()->json([
            'success' => true,
            'message' => 'Hari libur berhasil diupdate.',
            'data'    => $holiday,
        ]);
    }
 
    // DELETE /api/school-holidays/{id}
    public function destroy($id)
    {
        SchoolHoliday::findOrFail($id)->delete();
 
        return response()->json([
            'success' => true,
            'message' => 'Hari libur berhasil dihapus.',
        ]);
    }
}
 
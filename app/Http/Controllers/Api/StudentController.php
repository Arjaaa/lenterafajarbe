<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StudentService;
use App\Models\User;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    // GET all
    public function index()
    {
        return response()->json($this->studentService->getAll());
    }

    // POST (Teacher only)
public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'nis' => 'required|unique:students',
        'birth_date' => 'required|date',
        'parent_id' => 'required|exists:users,id'
    ]);

    $parent = User::find($request->parent_id);

    if ($parent->role !== 'parent') {
        return response()->json([
            'message' => 'Parent tidak valid'
        ], 400);
    }

    // 🔥 INI YANG KAMU LUPA
    $student = $this->studentService->create($request->all());

    return response()->json([
        'message' => 'Student created',
        'data' => $student
    ], 201);
}

    // GET by ID
    public function show($id)
    {
        return response()->json(
            $this->studentService->find($id)
        );
    }

    // UPDATE (Teacher only)
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'nis' => 'required',
            'birth_date' => 'required|date'
        ]);

        $student = $this->studentService->update($id, $request->all());

        return response()->json([
            'message' => 'Updated',
            'data' => $student
        ]);
    }

    // DELETE (Teacher only)
    public function destroy($id)
    {
        $this->studentService->delete($id);

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}
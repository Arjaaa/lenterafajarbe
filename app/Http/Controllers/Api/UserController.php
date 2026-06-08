<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
     // GET /api/users
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'role', 'phone', 'gender', 'created_at')
            ->latest();
 
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
 
        return response()->json([
            'success' => true,
            'data'    => $query->get(),
        ]);
    
}
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
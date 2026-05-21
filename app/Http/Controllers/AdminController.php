<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email_username' => 'required',
            'password' => 'required',
        ]);

        $admin = User::where('email', $request->email_username)
            ->orWhere('username', $request->email_username)
            ->where('role', 'admin')
            ->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Admin tidak ditemukan'
            ], 404);
        }

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Password salah'
            ], 401);
        }

        return response()->json([
            'message' => 'Login admin berhasil',
            'data' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'role' => $admin->role,
            ]
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'no_telepon' => 'required|string|max:20',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,anggota',
        ]);

        $user = User::create([
            'name' => $request->nama_lengkap,
            'nama_lengkap' => $request->nama_lengkap,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $nomorAnggota = null;

        if ($request->role === 'anggota') {
            $nomorAnggota = 'AGT' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

            Anggota::create([
                'nomor_anggota' => $nomorAnggota,
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Akun berhasil dibuat',
            'data' => [
                'id' => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
            ]
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:admin,anggota',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau email tidak ditemukan',
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password salah',
            ], 401);
        }

        if ($user->role !== $request->role) {
            return response()->json([
                'status' => false,
                'message' => 'Role login tidak sesuai',
            ], 403);
        }

        $nomorAnggota = null;

        if ($user->role === 'anggota') {
            $anggota = Anggota::where('email', $user->email)
                ->orWhere('username', $user->username)
                ->first();

            $nomorAnggota = $anggota ? $anggota->nomor_anggota : null;
        }

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil sebagai ' . $user->role,
            'data' => [
                'id' => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
            ]
        ], 200);
    }

    // FORGOT PASSWORD
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email',
        ]);

        $user = User::where('username', $request->username)
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Username dan email tidak cocok',
            ], 404);
        }

        $token = Str::random(60);

        $user->reset_token = $token;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Verifikasi berhasil, silakan reset password',
            'reset_token' => $token,
        ], 200);
    }

    // RESET PASSWORD
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('reset_token', $request->reset_token)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid',
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil direset',
        ], 200);
    }
}
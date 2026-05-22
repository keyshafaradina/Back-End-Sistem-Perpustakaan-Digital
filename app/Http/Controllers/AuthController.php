<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'no_telepon' => 'required|string|max:20',
            'username' => 'required|string|unique:users,username',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol',
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
            'role' => 'anggota',
        ]);

        $nomorAnggota = 'AGT' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

        Anggota::create([
            'user_id' => $user->id,
            'nomor_anggota' => $nomorAnggota,
            'nama_lengkap' => $request->nama_lengkap,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Akun anggota berhasil dibuat',
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
            $anggota = Anggota::where('user_id', $user->id)->first();
            $nomorAnggota = $anggota?->nomor_anggota;
        }

        $wajibIsiNama = $user->role === 'admin' && empty($user->nama_lengkap);

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil sebagai ' . $user->role,
            'data' => [
                'id' => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->username,
                'alamat' => $user->alamat,
                'no_telepon' => $user->no_telepon,
                'jabatan' => $user->role === 'admin' ? 'Pustakawan' : null,
                'role' => $user->role,
                'wajib_isi_nama' => $wajibIsiNama,
                'redirect' => $user->role === 'admin'
                    ? '/profil-admin'
                    : '/dashboard',
            ]
        ], 200);
    }

    public function updateProfilAdmin(Request $request, int $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
        ]);

        $user = User::where('id', $id)
            ->where('role', 'admin')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Admin tidak ditemukan',
            ], 404);
        }

        $user->update([
            'name' => $request->nama_lengkap,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Nama petugas berhasil disimpan',
            'data' => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->username,
                'alamat' => $user->alamat,
                'jabatan' => 'Pustakawan',
                'role' => $user->role,
            ]
        ], 200);
    }

    public function updateProfilAnggota(Request $request, int $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:20',
        ]);

        $user = User::where('id', $id)
            ->where('role', 'anggota')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Anggota tidak ditemukan',
            ], 404);
        }

        $user->update([
            'name' => $request->nama_lengkap,
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ]);

        Anggota::where('user_id', $user->id)->update([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profil anggota berhasil diperbarui',
            'data' => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->username,
                'alamat' => $user->alamat,
                'no_telepon' => $user->no_telepon,
                'role' => $user->role,
            ]
        ], 200);
    }

    public function logout()
    {
        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil',
            'redirect' => '/register',
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $passwordHash = Hash::make($request->password);

        $user->update([
            'password' => $passwordHash,
        ]);

        if ($user->role === 'anggota') {
            Anggota::where('user_id', $user->id)->update([
                'password' => $passwordHash,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah',
        ], 200);
    }
}
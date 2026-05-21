<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Anggota;

class AnggotaController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota',
            'nama' => 'required',
            'username' => 'required|unique:anggotas,username',
            'email' => 'required|email|unique:anggotas,email',
            'password' => 'required|min:6',
            'alamat' => 'nullable',
            'no_telepon' => 'nullable',
        ]);

        $anggota = Anggota::create([
            'nomor_anggota' => $request->nomor_anggota,
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'qr_code' => $request->nomor_anggota,
        ]);

        return response()->json([
            'message' => 'Registrasi anggota berhasil',
            'data' => $anggota
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_username' => 'required',
            'password' => 'required',
        ]);

        $anggota = Anggota::where('email', $request->email_username)
            ->orWhere('username', $request->email_username)
            ->first();

        if (!$anggota) {
            return response()->json([
                'message' => 'Akun anggota tidak ditemukan'
            ], 404);
        }

        if (!Hash::check($request->password, $anggota->password)) {
            return response()->json([
                'message' => 'Password salah'
            ], 401);
        }

        return response()->json([
            'message' => 'Login anggota berhasil',
            'data' => [
                'id' => $anggota->id,
                'nomor_anggota' => $anggota->nomor_anggota,
                'nama' => $anggota->nama,
                'username' => $anggota->username,
                'email' => $anggota->email,
                'role' => 'anggota'
            ]
        ]);
    }
}
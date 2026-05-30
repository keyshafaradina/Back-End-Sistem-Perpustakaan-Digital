<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Anggota;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Data anggota berhasil diambil',
            'data' => $anggotas
        ]);
    }

    public function show($id)
    {
        $anggota = Anggota::find($id);

        if (!$anggota) {
            return response()->json([
                'status' => false,
                'message' => 'Anggota tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail anggota berhasil diambil',
            'data' => $anggota
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota',
            'nama_lengkap' => 'required',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'email' => 'required|email|unique:anggotas,email',
            'no_telepon' => 'nullable',
            'username' => 'required|unique:anggotas,username',
            'password' => 'required|min:6',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoName = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/profile'), $fotoName);
        }

        $anggota = Anggota::create([
            'nomor_anggota' => $request->nomor_anggota,
            'nama_lengkap' => $request->nama_lengkap,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'qr_code' => $request->nomor_anggota,
            'foto' => $fotoName,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Anggota berhasil ditambahkan',
            'data' => $anggota
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $anggota = Anggota::find($id);

        if (!$anggota) {
            return response()->json([
                'status' => false,
                'message' => 'Anggota tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama_lengkap' => 'required',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'no_telepon' => 'nullable',
            'username' => 'required|unique:anggotas,username,' . $id,
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if (
                $anggota->foto &&
                file_exists(public_path('uploads/profile/' . $anggota->foto))
            ) {
                unlink(public_path('uploads/profile/' . $anggota->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/profile'), $filename);

            $anggota->foto = $filename;
        }

        $anggota->nama_lengkap = $request->nama_lengkap;
        $anggota->tanggal_lahir = $request->tanggal_lahir ?? $anggota->tanggal_lahir;
        $anggota->username = $request->username;
        $anggota->alamat = $request->alamat;
        $anggota->no_telepon = $request->no_telepon;

        $anggota->save();

        return response()->json([
            'status' => true,
            'message' => 'Data anggota berhasil diperbarui',
            'data' => $anggota
        ]);
    }

    public function destroy($id)
    {
        $anggota = Anggota::find($id);

        if (!$anggota) {
            return response()->json([
                'status' => false,
                'message' => 'Anggota tidak ditemukan'
            ], 404);
        }

        if (
            $anggota->foto &&
            file_exists(public_path('uploads/profile/' . $anggota->foto))
        ) {
            unlink(public_path('uploads/profile/' . $anggota->foto));
        }

        $anggota->delete();

        return response()->json([
            'status' => true,
            'message' => 'Anggota berhasil dihapus'
        ]);
    }
}
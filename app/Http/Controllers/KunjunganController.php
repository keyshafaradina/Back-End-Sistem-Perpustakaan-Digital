<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKunjungan;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    public function simpan(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required',
            'nama' => 'required',
        ]);

        $kunjungan = LaporanKunjungan::create([
            'nomor_anggota' => $request->nomor_anggota,
            'nama' => $request->nama,
            'tanggal_kunjungan' => Carbon::now()->toDateString(),
            'jam_kunjungan' => Carbon::now()->format('H:i:s'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Kunjungan berhasil disimpan',
            'data' => [
                'nomor_anggota' => $kunjungan->nomor_anggota,
                'nama' => $kunjungan->nama,
                'tanggal' => $kunjungan->tanggal_kunjungan,
                'jam' => $kunjungan->jam_kunjungan,
            ]
        ], 201);
    }
}
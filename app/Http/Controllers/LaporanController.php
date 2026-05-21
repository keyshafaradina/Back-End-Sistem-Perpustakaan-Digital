<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with([
            'anggota',
            'petugas',
            'detailPeminjaman.buku'
        ]);

        if ($request->tanggal) {
            $query->whereDay('tanggal_peminjaman', $request->tanggal);
        }

        if ($request->bulan) {
            $query->whereMonth('tanggal_peminjaman', $request->bulan);
        }

        if ($request->tahun) {
            $query->whereYear('tanggal_peminjaman', $request->tahun);
        }

        $data = $query->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Data laporan peminjaman dan pengembalian berhasil diambil',
            'data' => $data
        ]);
    }
}
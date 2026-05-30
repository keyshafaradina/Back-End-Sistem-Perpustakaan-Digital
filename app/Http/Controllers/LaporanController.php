<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\LaporanKunjungan;
use App\Models\Anggota;

class LaporanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SIMPAN KUNJUNGAN PERPUSTAKAAN
    |--------------------------------------------------------------------------
    */
    public function simpanKunjungan(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required|string',
            'nama' => 'required|string',
        ]);

        $anggota = Anggota::where('nomor_anggota', $request->nomor_anggota)
            ->where('nama_lengkap', $request->nama)
            ->first();

        if (!$anggota) {
            return response()->json([
                'status' => false,
                'message' => 'Kunjungan gagal, data anggota perpustakaan tidak ditemukan',
            ], 404);
        }

        $waktuSekarang = now('Asia/Jakarta');

        LaporanKunjungan::create([
            'nomor_anggota' => $anggota->nomor_anggota,
            'nama' => $anggota->nama_lengkap,
            'tanggal_kunjungan' => $waktuSekarang->format('Y-m-d'),
            'jam_kunjungan' => $waktuSekarang->format('H:i:s'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Kunjungan berhasil disimpan',
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | LAPORAN KUNJUNGAN
    |--------------------------------------------------------------------------
    */
    public function laporanKunjungan(Request $request)
    {
        $query = LaporanKunjungan::query();

        if ($request->filled('tanggal')) {
            $query->whereDay('tanggal_kunjungan', $request->tanggal);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_kunjungan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_kunjungan', $request->tahun);
        }

        $data = $query->latest()->get();

        $hasil = $data->map(function ($item) {
            return [
                'nomor_anggota' => $item->nomor_anggota,
                'nama' => $item->nama,
                'tanggal' => $item->tanggal_kunjungan,
                'jam' => $item->jam_kunjungan,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Laporan kunjungan berhasil diambil',
            'data' => $hasil,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LAPORAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */
    public function laporanPeminjaman(Request $request)
    {
        $query = Peminjaman::with([
            'anggota',
            'detailPeminjaman.buku',
        ]);

        if ($request->filled('tanggal')) {
            $query->whereDay('tanggal_peminjaman', $request->tanggal);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_peminjaman', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_peminjaman', $request->tahun);
        }

        $data = $query->latest()->get();

        $hasil = $data->map(function ($item) {
            return [
                'nama' => $item->anggota->nama_lengkap ?? '-',
                'tanggal_pinjam' => $item->tanggal_peminjaman,
                'buku' => $item->detailPeminjaman->first()?->buku?->judul ?? '-',
                'status' => $item->status,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Laporan peminjaman berhasil diambil',
            'data' => $hasil,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LAPORAN PENGEMBALIAN
    |--------------------------------------------------------------------------
    */
    public function laporanPengembalian(Request $request)
    {
        $query = Peminjaman::with([
            'anggota',
            'detailPeminjaman.buku',
        ])->whereNotNull('tanggal_dikembalikan');

        if ($request->filled('tanggal')) {
            $query->whereDay('tanggal_dikembalikan', $request->tanggal);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_dikembalikan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_dikembalikan', $request->tahun);
        }

        $data = $query->latest()->get();

        $hasil = $data->map(function ($item) {
            return [
                'nama' => $item->anggota->nama_lengkap ?? '-',
                'tanggal_pinjam' => $item->tanggal_peminjaman,
                'tanggal_pengembalian' => $item->tanggal_dikembalikan,
                'buku' => $item->detailPeminjaman->first()?->buku?->judul ?? '-',
                'status' => $item->status,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Laporan pengembalian berhasil diambil',
            'data' => $hasil,
        ]);
    }
}
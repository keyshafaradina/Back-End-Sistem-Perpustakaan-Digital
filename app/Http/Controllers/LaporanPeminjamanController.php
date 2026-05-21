<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanPeminjamanController extends Controller
{
    public function index()
    {
        $peminjamans = Peminjaman::with([
            'anggota',
            'petugas',
            'detailPeminjaman.buku'
        ])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diambil',
            'data' => $peminjamans
        ]);
    }

    public function formPerpanjangan($id)
    {
        $peminjaman = Peminjaman::with([
            'anggota',
            'petugas',
            'detailPeminjaman.buku'
        ])->find($id);

        if (!$peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail peminjaman berhasil diambil',
            'data' => $peminjaman
        ]);
    }

    public function ajukanPerpanjangan($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan'
            ], 404);
        }

        if ($peminjaman->status !== 'dipinjam') {
            return response()->json([
                'success' => false,
                'message' => 'Buku sudah dikembalikan, tidak bisa diperpanjang'
            ], 400);
        }

        if ($peminjaman->status_perpanjangan === 'diajukan') {
            return response()->json([
                'success' => false,
                'message' => 'Perpanjangan sudah diajukan dan menunggu persetujuan admin'
            ], 400);
        }

        if ($peminjaman->status_perpanjangan === 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman ini sudah pernah diperpanjang'
            ], 400);
        }

        $hariIni = Carbon::now()->startOfDay();
        $tanggalPengembalianLama = Carbon::parse($peminjaman->tanggal_pengembalian)->startOfDay();
        $batasPerpanjangan = $tanggalPengembalianLama->copy()->subDays(3);

        if ($hariIni->gt($batasPerpanjangan)) {
            return response()->json([
                'success' => false,
                'message' => 'Perpanjangan hanya boleh maksimal H-3 sebelum pengembalian',
                'batas_perpanjangan' => $batasPerpanjangan->format('Y-m-d')
            ], 400);
        }

        $tanggalPengembalianBaru = $tanggalPengembalianLama->copy()->addDays(7);

        $peminjaman->update([
            'tanggal_perpanjangan' => $hariIni->format('Y-m-d'),
            'tanggal_pengembalian_baru' => $tanggalPengembalianBaru->format('Y-m-d'),
            'status_perpanjangan' => 'diajukan',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perpanjangan berhasil diajukan, menunggu persetujuan admin',
            'tanggal_pengembalian_lama' => $tanggalPengembalianLama->format('Y-m-d'),
            'tanggal_pengembalian_baru' => $tanggalPengembalianBaru->format('Y-m-d'),
            'data' => $peminjaman
        ]);
    }

    public function setujuiPerpanjangan($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan'
            ], 404);
        }

        if ($peminjaman->status_perpanjangan !== 'diajukan') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pengajuan perpanjangan yang perlu disetujui'
            ], 400);
        }

        $peminjaman->update([
            'tanggal_pengembalian' => $peminjaman->tanggal_pengembalian_baru,
            'status_perpanjangan' => 'disetujui',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perpanjangan berhasil disetujui admin',
            'data' => $peminjaman
        ]);
    }

    public function tolakPerpanjangan($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan'
            ], 404);
        }

        if ($peminjaman->status_perpanjangan !== 'diajukan') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pengajuan perpanjangan yang bisa ditolak'
            ], 400);
        }

        $peminjaman->update([
            'tanggal_pengembalian_baru' => null,
            'status_perpanjangan' => 'ditolak',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perpanjangan berhasil ditolak admin',
            'data' => $peminjaman
        ]);
    }
}
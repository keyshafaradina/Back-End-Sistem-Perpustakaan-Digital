<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Carbon\Carbon;

class LaporanPeminjamanController extends Controller
{
    public function index()
    {
        $peminjamans = Peminjaman::with([
            'anggota',
            'petugas',
            'detailPeminjaman.buku'
        ])
            ->where('status', '!=', 'dikembalikan')
            ->whereNotNull('status_perpanjangan')
            ->where('status_perpanjangan', '!=', 'belum')
            ->orderByRaw("
                CASE 
                    WHEN status_perpanjangan = 'diajukan' THEN 1
                    WHEN status_perpanjangan = 'disetujui' THEN 2
                    WHEN status_perpanjangan = 'ditolak' THEN 3
                    ELSE 4
                END
            ")
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diambil',
            'data' => $peminjamans
        ]);
    }

    public function formPerpanjangan(int $id)
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

    public function ajukanPerpanjangan(int $id)
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
                'message' => 'Perpanjangan sebelumnya sudah disetujui'
            ], 400);
        }

        $tanggalPengembalianLama = Carbon::parse($peminjaman->tanggal_pengembalian);
        $tanggalPengembalianBaru = $tanggalPengembalianLama->copy()->addDays(7);

        $peminjaman->update([
            'tanggal_perpanjangan' => now()->format('Y-m-d'),
            'tanggal_pengembalian_baru' => $tanggalPengembalianBaru->format('Y-m-d'),
            'status_perpanjangan' => 'diajukan',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perpanjangan berhasil diajukan, menunggu persetujuan admin',
            'data' => $peminjaman
        ]);
    }

    public function setujuiPerpanjangan(int $id)
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

        $tanggalBaru = $peminjaman->tanggal_pengembalian_baru;

        if (!$tanggalBaru) {
            $tanggalBaru = Carbon::parse($peminjaman->tanggal_pengembalian)
                ->addDays(7)
                ->format('Y-m-d');
        }

        $peminjaman->update([
            'tanggal_pengembalian' => $tanggalBaru,
            'tanggal_pengembalian_baru' => null,
            'status_perpanjangan' => 'disetujui',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perpanjangan berhasil disetujui admin',
            'data' => $peminjaman
        ]);
    }

    public function tolakPerpanjangan(int $id)
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
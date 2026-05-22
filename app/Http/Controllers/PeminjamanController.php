<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with([
            'anggota',
            'petugas',
            'detailPeminjaman.buku'
        ])->latest()->get();

        return response()->json([
            'message' => 'Data peminjaman berhasil diambil',
            'data' => $peminjaman
        ]);
    }

    public function scanAnggota(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required'
        ]);

        $anggota = Anggota::where('nomor_anggota', $request->nomor_anggota)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Anggota ditemukan',
            'data' => $anggota
        ]);
    }

    public function cariBuku(Request $request)
    {
        $request->validate([
            'kode_buku' => 'required'
        ]);

        $buku = Buku::where('kode_buku', $request->kode_buku)
            ->where('status', 'aktif')
            ->where('stok', '>', 0)
            ->first();

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan atau stok habis'], 404);
        }

        return response()->json([
            'message' => 'Buku ditemukan',
            'data' => $buku
        ]);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'petugas_id' => 'nullable|exists:users,id',
            'tanggal_peminjaman' => 'required|date',
            'buku_ids' => 'required|array|min:1',
            'buku_ids.*' => 'required|exists:bukus,id',
        ]);

        DB::beginTransaction();

        try {
            $tanggalPeminjaman = Carbon::parse($request->tanggal_peminjaman)->startOfDay();
            $tanggalPengembalian = $tanggalPeminjaman->copy()->addDays(7);

            $peminjaman = Peminjaman::create([
                'anggota_id' => $request->anggota_id,
                'petugas_id' => $request->petugas_id,
                'tanggal_peminjaman' => $tanggalPeminjaman->format('Y-m-d'),
                'tanggal_pengembalian' => $tanggalPengembalian->format('Y-m-d'),
                'tanggal_dikembalikan' => null,
                'tanggal_perpanjangan' => null,
                'status' => 'dipinjam',
                'status_perpanjangan' => 'belum',
            ]);

            foreach ($request->buku_ids as $buku_id) {
                $buku = Buku::where('id', $buku_id)
                    ->where('status', 'aktif')
                    ->where('stok', '>', 0)
                    ->first();

                if (!$buku) {
                    DB::rollBack();
                    return response()->json(['message' => 'Salah satu buku tidak tersedia'], 400);
                }

                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'buku_id' => $buku->id,
                ]);

                $stokBaru = $buku->stok - 1;

                $buku->update([
                    'stok' => $stokBaru,
                    'ketersediaan' => $stokBaru > 0 ? 'tersedia' : 'dipinjam',
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data peminjaman berhasil disimpan',
                'data' => $peminjaman
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan peminjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function kembalikan(Request $request, int $id)
    {
        $request->validate([
            'tanggal_dikembalikan' => 'required|date',
        ]);

        $peminjaman = Peminjaman::with('detailPeminjaman.buku')->find($id);

        if (!$peminjaman) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }

        foreach ($peminjaman->detailPeminjaman as $detail) {
            if ($detail->buku) {
                $detail->buku->update([
                    'stok' => $detail->buku->stok + 1,
                    'ketersediaan' => 'tersedia',
                ]);
            }
        }

        $peminjaman->update([
            'tanggal_dikembalikan' => $request->tanggal_dikembalikan,
            'status' => 'dikembalikan',
        ]);

        return response()->json([
            'message' => 'Buku berhasil dikembalikan',
            'data' => $peminjaman
        ]);
    }

    public function batal()
    {
        return response()->json([
            'message' => 'Peminjaman dibatalkan'
        ]);
    }
}
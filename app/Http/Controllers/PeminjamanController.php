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
            'data' => $peminjaman->map(function ($item) {
                return $this->formatPeminjaman($item);
            })
        ]);
    }

    public function scanAnggota(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required'
        ]);

        $anggota = Anggota::where('nomor_anggota', $request->nomor_anggota)->first();

        if (!$anggota) {
            return response()->json([
                'message' => 'Anggota tidak ditemukan'
            ], 404);
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
            ->where('ketersediaan', 'tersedia')
            ->where('stok', '>', 0)
            ->first();

        if (!$buku) {
            return response()->json([
                'message' => 'Buku tidak ditemukan atau sedang tidak tersedia'
            ], 404);
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
                    ->where('ketersediaan', 'tersedia')
                    ->where('stok', '>', 0)
                    ->first();

                if (!$buku) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Salah satu buku tidak tersedia'
                    ], 400);
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

            $peminjaman->load([
                'anggota',
                'petugas',
                'detailPeminjaman.buku'
            ]);

            return response()->json([
                'message' => 'Data peminjaman berhasil disimpan',
                'data' => $this->formatPeminjaman($peminjaman)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan peminjaman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function kembalikan(Request $request, $id)
    {
        $request->validate([
            'tanggal_dikembalikan' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with([
                'anggota',
                'petugas',
                'detailPeminjaman.buku'
            ])->findOrFail($id);

            if ($peminjaman->status === 'dikembalikan' || $peminjaman->status === 'terlambat') {
                return response()->json([
                    'message' => 'Buku sudah pernah dikembalikan'
                ], 400);
            }

            foreach ($peminjaman->detailPeminjaman as $detail) {
                $buku = $detail->buku;

                if ($buku) {
                    $stokBaru = $buku->stok + 1;

                    $buku->update([
                        'stok' => $stokBaru,
                        'ketersediaan' => 'tersedia',
                    ]);
                }
            }

            $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_pengembalian)->startOfDay();
            $tanggalDikembalikan = Carbon::parse($request->tanggal_dikembalikan)->startOfDay();

            $terlambatHari = 0;
            $status = 'dikembalikan';

            if ($tanggalDikembalikan->gt($tanggalBatasKembali)) {
                $terlambatHari = $tanggalBatasKembali->diffInDays($tanggalDikembalikan);
                $status = 'terlambat';
            }

            $peminjaman->update([
                'tanggal_dikembalikan' => $tanggalDikembalikan->format('Y-m-d'),
                'status' => $status,
            ]);

            DB::commit();

            $peminjaman->refresh();
            $peminjaman->load([
                'anggota',
                'petugas',
                'detailPeminjaman.buku'
            ]);

            return response()->json([
                'message' => $status === 'terlambat'
                    ? 'Buku berhasil dikembalikan, terlambat ' . $terlambatHari . ' hari'
                    : 'Buku berhasil dikembalikan',
                'terlambat_hari' => $terlambatHari,
                'data' => $this->formatPeminjaman($peminjaman, $terlambatHari)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal mengembalikan buku',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function batal()
    {
        return response()->json([
            'message' => 'Peminjaman dibatalkan'
        ]);
    }

    private function formatPeminjaman($peminjaman, $terlambatManual = null)
    {
        $hariIni = Carbon::now()->startOfDay();
        $tanggalBatasKembali = Carbon::parse($peminjaman->tanggal_pengembalian)->startOfDay();

        $terlambatHari = 0;
        $keterangan = 'Belum terlambat';

        if ($terlambatManual !== null) {
            $terlambatHari = $terlambatManual;
            $keterangan = $terlambatHari > 0
                ? 'Buku terlambat ' . $terlambatHari . ' hari'
                : 'Belum terlambat';
        } elseif (
            $peminjaman->tanggal_dikembalikan === null &&
            $peminjaman->status === 'dipinjam' &&
            $hariIni->gt($tanggalBatasKembali)
        ) {
            $terlambatHari = $tanggalBatasKembali->diffInDays($hariIni);
            $keterangan = 'Buku terlambat ' . $terlambatHari . ' hari';
        }

        return [
            'id_peminjaman' => $peminjaman->id,

            'anggota' => $peminjaman->anggota ? [
                'id' => $peminjaman->anggota->id,
                'nomor_anggota' => $peminjaman->anggota->nomor_anggota,
                'nama_lengkap' => $peminjaman->anggota->nama_lengkap,
            ] : null,

            'petugas' => $peminjaman->petugas ? [
                'id' => $peminjaman->petugas->id,
                'nama_lengkap' => $peminjaman->petugas->nama_lengkap,
            ] : null,

            'buku' => $peminjaman->detailPeminjaman->map(function ($detail) {
                return $detail->buku ? [
                    'id' => $detail->buku->id,
                    'kode_buku' => $detail->buku->kode_buku,
                    'judul' => $detail->buku->judul,
                ] : null;
            })->filter()->values(),

            'tanggal_peminjaman' => $peminjaman->tanggal_peminjaman,
            'tanggal_pengembalian' => $peminjaman->tanggal_pengembalian,
            'tanggal_dikembalikan' => $peminjaman->tanggal_dikembalikan,
            'tanggal_perpanjangan' => $peminjaman->tanggal_perpanjangan,

            'status' => $peminjaman->status,
            'status_perpanjangan' => $peminjaman->status_perpanjangan,
            'terlambat_hari' => $terlambatHari,
            'keterangan_keterlambatan' => $keterangan,
        ];
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\DashboardSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::now()->startOfDay();

        $totalBuku = Buku::where('status', 'aktif')->count();
        $totalAnggota = Anggota::count();

        $dipinjam = Peminjaman::where('status', 'dipinjam')->count();

        $terlambat = Peminjaman::where('status', 'dipinjam')
            ->whereDate('tanggal_pengembalian', '<', $hariIni)
            ->count();

        $permohonanPerpanjangan = Peminjaman::where('status_perpanjangan', 'diajukan')
            ->count();

        $akanJatuhTempo = Peminjaman::where('status', 'dipinjam')
            ->whereDate('tanggal_pengembalian', '>=', $hariIni)
            ->whereDate('tanggal_pengembalian', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Dashboard berhasil diambil',
            'data' => [
                'total_buku' => $totalBuku,
                'total_anggota' => $totalAnggota,
                'dipinjam' => $dipinjam,
                'terlambat' => $terlambat,
                'permohonan_perpanjangan' => $permohonanPerpanjangan,
                'akan_jatuh_tempo' => $akanJatuhTempo,
                'buku_populer' => $this->getBukuPopulerData(),
            ]
        ]);
    }

    public function bukuPopuler()
    {
        return response()->json([
            'status' => true,
            'message' => 'Data buku populer berhasil diambil',
            'data' => $this->getBukuPopulerData()
        ]);
    }

    private function getBukuPopulerData()
    {
        return Buku::withCount([
                'detailPeminjaman as total_dipinjam'
            ])
            ->where('status', 'aktif')
            ->orderByDesc('total_dipinjam')
            ->limit(10)
            ->get()
            ->map(function ($buku, $index) {
                return [
                    'ranking' => $index + 1,
                    'id' => $buku->id,
                    'kode_buku' => $buku->kode_buku,
                    'judul' => $buku->judul,
                    'penulis' => $buku->penulis,
                    'gambar' => $buku->gambar,
                    'stok' => $buku->stok,
                    'ketersediaan' => $buku->ketersediaan,
                    'total_dipinjam' => $buku->total_dipinjam,
                ];
            });
    }

    public function terlambat()
    {
        $hariIni = Carbon::now()->startOfDay();

        $data = Peminjaman::with([
                'anggota',
                'detailPeminjaman.buku'
            ])
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_pengembalian', '<', $hariIni)
            ->latest()
            ->get()
            ->map(function ($item) use ($hariIni) {
                $tanggalPengembalian = Carbon::parse($item->tanggal_pengembalian)->startOfDay();

                return [
                    'id_peminjaman' => $item->id,
                    'anggota' => [
                        'id' => $item->anggota->id ?? null,
                        'nama_lengkap' => $item->anggota->nama_lengkap ?? '-',
                    ],
                    'buku' => $item->detailPeminjaman->map(function ($detail) {
                        return [
                            'id' => $detail->buku->id ?? null,
                            'judul' => $detail->buku->judul ?? '-',
                        ];
                    }),
                    'tanggal_peminjaman' => $item->tanggal_peminjaman,
                    'tanggal_pengembalian' => $item->tanggal_pengembalian,
                    'terlambat_hari' => $tanggalPengembalian->diffInDays($hariIni),
                    'status' => 'terlambat',
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Data keterlambatan berhasil diambil',
            'data' => $data
        ]);
    }

    public function permohonanPerpanjangan()
    {
        $data = Peminjaman::with([
                'anggota',
                'detailPeminjaman.buku'
            ])
            ->where('status_perpanjangan', 'diajukan')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data permohonan perpanjangan berhasil diambil',
            'data' => $data
        ]);
    }

    public function jatuhTempo()
    {
        $hariIni = Carbon::now()->format('Y-m-d');
        $hPlus3 = Carbon::now()->addDays(3)->format('Y-m-d');

        $data = Peminjaman::with([
                'anggota',
                'detailPeminjaman.buku'
            ])
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_pengembalian', '>=', $hariIni)
            ->whereDate('tanggal_pengembalian', '<=', $hPlus3)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data jatuh tempo berhasil diambil',
            'data' => $data
        ]);
    }

    public function editDashboard()
    {
        $dashboard = DashboardSetting::first();

        return response()->json([
            'status' => true,
            'message' => 'Data dashboard berhasil diambil',
            'data' => $dashboard
        ]);
    }

    public function updateDashboard(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|array',
            'alamat' => 'required|string',
            'akun_sosmed' => 'required|string',
            'logo' => 'nullable|string'
        ]);

        $dashboard = DashboardSetting::firstOrNew();

        $dashboard->judul = $request->judul;
        $dashboard->visi = $request->visi;
        $dashboard->misi = $request->misi;
        $dashboard->alamat = $request->alamat;
        $dashboard->akun_sosmed = $request->akun_sosmed;

        if ($request->logo) {
            $dashboard->logo = $request->logo;
        }

        $dashboard->save();

        return response()->json([
            'status' => true,
            'message' => 'Dashboard berhasil diupdate',
            'data' => $dashboard
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::where('status', 'aktif')->get();

        return response()->json([
            'message' => 'Data buku berhasil diambil',
            'data' => $buku
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku',
            'judul' => 'required',
            'penulis' => 'required',
            'stok' => 'required|integer|min:0',
        ]);

        $buku = Buku::create([
            'kode_buku' => $request->kode_buku,
            'judul' => $request->judul,
            'sinopsis' => $request->sinopsis,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
            'stok' => $request->stok,
            'nomor_rak' => $request->nomor_rak,
            'gambar' => $request->gambar,
            'status' => 'aktif',
            'ketersediaan' => $request->stok > 0 ? 'tersedia' : 'tidak tersedia',
        ]);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku
        ], 201);
    }

    public function show($id)
    {
        $buku = Buku::findOrFail($id);

        return response()->json([
            'message' => 'Detail buku berhasil diambil',
            'data' => $buku
        ]);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku,' . $id,
            'judul' => 'required',
            'penulis' => 'required',
            'stok' => 'required|integer|min:0',
        ]);

        $buku->update([
            'kode_buku' => $request->kode_buku,
            'judul' => $request->judul,
            'sinopsis' => $request->sinopsis,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
            'stok' => $request->stok,
            'nomor_rak' => $request->nomor_rak,
            'gambar' => $request->gambar,
            'ketersediaan' => $request->stok > 0 ? 'tersedia' : 'tidak tersedia',
        ]);

        return response()->json([
            'message' => 'Buku berhasil diupdate',
            'data' => $buku
        ]);
    }

    public function arsipkan($id)
    {
        $buku = Buku::findOrFail($id);

        $buku->update([
            'status' => 'diarsipkan',
            'ketersediaan' => 'tidak tersedia',
        ]);

        return response()->json([
            'message' => 'Buku berhasil diarsipkan',
            'data' => $buku
        ]);
    }

    public function diarsipkan()
    {
        $buku = Buku::where('status', 'diarsipkan')->get();

        return response()->json([
            'message' => 'Data buku diarsipkan',
            'data' => $buku
        ]);
    }

    public function bukaArsip($id)
    {
        $buku = Buku::findOrFail($id);

        $buku->update([
            'status' => 'aktif',
            'ketersediaan' => $buku->stok > 0 ? 'tersedia' : 'tidak tersedia',
        ]);

        return response()->json([
            'message' => 'Arsip buku berhasil dibuka',
            'data' => $buku
        ]);
    }

    public function hapuskan($id)
    {
        $buku = Buku::findOrFail($id);

        $buku->update([
            'status' => 'dihapus',
            'ketersediaan' => 'tidak tersedia',
        ]);

        return response()->json([
            'message' => 'Buku berhasil dipindahkan ke data dihapus',
            'data' => $buku
        ]);
    }

    public function dihapus()
    {
        $buku = Buku::where('status', 'dihapus')->get();

        return response()->json([
            'message' => 'Data buku dihapus',
            'data' => $buku
        ]);
    }

    public function pulihkan($id)
    {
        $buku = Buku::findOrFail($id);

        $buku->update([
            'status' => 'aktif',
            'ketersediaan' => $buku->stok > 0 ? 'tersedia' : 'tidak tersedia',
        ]);

        return response()->json([
            'message' => 'Buku berhasil dipulihkan',
            'data' => $buku
        ]);
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus permanen'
        ]);
    }
}
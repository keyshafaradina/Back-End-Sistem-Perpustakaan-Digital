<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'bukus';

    protected $fillable = [
        'kode_buku',
        'judul',
        'sinopsis',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok',
        'nomor_rak',
        'gambar',
        'status',
        'ketersediaan',
    ];

    protected $attributes = [
        'status' => 'aktif',
        'ketersediaan' => 'tersedia',
    ];

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class, 'buku_id');
    }
}
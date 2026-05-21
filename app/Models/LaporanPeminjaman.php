<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPeminjaman extends Model
{
    protected $table = 'laporan_peminjaman';

    protected $fillable = [
        'nama',
        'tgl_pinjam',
        'tgl_kembali',
        'buku',
        'status',
    ];
}
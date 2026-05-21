<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';

    protected $fillable = [
        'anggota_id',
        'petugas_id',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'tanggal_pengembalian_baru',
        'tanggal_dikembalikan',
        'tanggal_perpanjangan',
        'status',
        'status_perpanjangan',
    ];

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'tanggal_pengembalian' => 'date',
        'tanggal_pengembalian_baru' => 'date',
        'tanggal_dikembalikan' => 'date',
        'tanggal_perpanjangan' => 'date',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id');
    }
}
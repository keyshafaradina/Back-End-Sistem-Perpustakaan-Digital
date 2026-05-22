<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKunjungan extends Model
{
    protected $fillable = [
        'nomor_anggota',
        'nama',
        'tanggal_kunjungan',
        'jam_kunjungan'
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $fillable = [
        'user_id',
        'nomor_anggota',
        'nama_lengkap',
        'tanggal_lahir',
        'alamat',
        'email',
        'no_telepon',
        'username',
        'password',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggotas';

    protected $fillable = [
        'nomor_anggota',
        'nama_lengkap',
        'tanggal_lahir',
        'alamat',
        'email',
        'no_telepon',
        'username',
        'password',
        'qr_code',
        'foto',
        'user_id',
    ];

    protected $hidden = [
        'password',
    ];
}
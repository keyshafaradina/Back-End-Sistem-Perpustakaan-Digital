<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSetting extends Model
{
    protected $fillable = [
        'judul',
        'visi',
        'misi',
        'alamat',
        'akun_sosmed',
        'logo'
    ];

    protected $casts = [
        'misi' => 'array'
    ];
}
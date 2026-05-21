<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anggota extends Authenticatable
{
    use HasFactory;

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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // otomatis hash password
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
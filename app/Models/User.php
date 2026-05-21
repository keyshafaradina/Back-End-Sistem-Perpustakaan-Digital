<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'nama_lengkap',
        'tanggal_lahir',
        'alamat',
        'email',
        'no_telepon',
        'username',
        'password',
        'role',
        'jabatan',
        'reset_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_token',
    ];
}
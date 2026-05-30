<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
    'foto',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
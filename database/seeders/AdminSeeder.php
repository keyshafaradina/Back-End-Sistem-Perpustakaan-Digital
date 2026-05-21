<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin E-Library',
            'nama_lengkap' => 'Admin E-Library',
            'tanggal_lahir' => '2000-01-01',
            'alamat' => 'Sekolah',
            'email' => 'admin@gmail.com',
            'no_telepon' => '08123456789',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'jabatan' => 'Admin',
        ]);
    }
}
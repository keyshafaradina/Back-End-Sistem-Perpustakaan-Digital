<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();

            $table->string('email')->unique();
            $table->string('no_telepon')->nullable();

            $table->string('username')->unique();
            $table->string('password');

            $table->enum('role', ['admin', 'anggota'])->default('anggota');

            $table->string('jabatan')->nullable();
            $table->string('reset_token')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
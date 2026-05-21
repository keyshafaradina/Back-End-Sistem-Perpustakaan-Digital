<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggotas', function (Blueprint $table) {

    $table->id();

    $table->string('nomor_anggota')->unique();

    $table->string('nama_lengkap');

    $table->date('tanggal_lahir');

    $table->text('alamat');

    $table->string('email')->unique();

    $table->string('no_telepon');

    $table->string('username')->unique();

    $table->string('password');

    $table->string('qr_code')->nullable();

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};
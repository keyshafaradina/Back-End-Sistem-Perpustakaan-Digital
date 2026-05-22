<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kunjungans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_anggota');
            $table->string('nama');
            $table->date('tanggal_kunjungan');
            $table->time('jam_kunjungan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kunjungans');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();

            $table->string('kode_buku')->unique();
            $table->string('judul');
            $table->text('sinopsis')->nullable();

            $table->string('penulis');
            $table->string('penerbit')->nullable();
            $table->year('tahun_terbit')->nullable();

            $table->integer('stok')->default(0);
            $table->string('nomor_rak')->nullable();
            $table->string('gambar')->nullable();

            $table->enum('status', [
                'aktif',
                'diarsipkan',
                'dihapus'
            ])->default('aktif');

            $table->enum('ketersediaan', [
                'tersedia',
                'dipinjam',
                'tidak tersedia'
            ])->default('tersedia');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_peminjaman', function (Blueprint $table) {

            // id otomatis
            $table->id();

            // nama peminjam
            $table->string('nama');

            // tanggal pinjam
            $table->date('tgl_pinjam');

            // tanggal kembali boleh kosong
            $table->date('tgl_kembali')->nullable();

            // nama buku
            $table->string('buku');

            // status peminjaman
            $table->enum('status', [
                'Dipinjam',
                'Dikembalikan',
                'Diperpanjang'
            ]);

            // created_at & updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_peminjaman');
    }
};
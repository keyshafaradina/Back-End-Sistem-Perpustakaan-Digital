<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anggota_id')
                ->constrained('anggotas')
                ->onDelete('cascade');

            $table->foreignId('petugas_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->date('tanggal_peminjaman');
            $table->date('tanggal_pengembalian');
            $table->date('tanggal_dikembalikan')->nullable();

            $table->enum('status', [
                'dipinjam',
                'dikembalikan',
                'terlambat'
            ])->default('dipinjam');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
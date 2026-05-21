<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {

            // tanggal perpanjangan
            if (!Schema::hasColumn('peminjamans', 'tanggal_perpanjangan')) {
                $table->date('tanggal_perpanjangan')
                    ->nullable()
                    ->after('tanggal_dikembalikan');
            }

            // tanggal pengembalian baru
            if (!Schema::hasColumn('peminjamans', 'tanggal_pengembalian_baru')) {
                $table->date('tanggal_pengembalian_baru')
                    ->nullable()
                    ->after('tanggal_perpanjangan');
            }

            // status perpanjangan
            if (!Schema::hasColumn('peminjamans', 'status_perpanjangan')) {
                $table->enum('status_perpanjangan', [
                    'belum',
                    'diajukan',
                    'disetujui',
                    'ditolak'
                ])->default('belum')
                  ->after('tanggal_pengembalian_baru');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {

            $columns = [];

            if (Schema::hasColumn('peminjamans', 'tanggal_perpanjangan')) {
                $columns[] = 'tanggal_perpanjangan';
            }

            if (Schema::hasColumn('peminjamans', 'tanggal_pengembalian_baru')) {
                $columns[] = 'tanggal_pengembalian_baru';
            }

            if (Schema::hasColumn('peminjamans', 'status_perpanjangan')) {
                $columns[] = 'status_perpanjangan';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('peminjamans', 'tanggal_pengembalian_baru')) {
            Schema::table('peminjamans', function (Blueprint $table) {
                $table->date('tanggal_pengembalian_baru')->nullable()->after('tanggal_pengembalian');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('peminjamans', 'tanggal_pengembalian_baru')) {
            Schema::table('peminjamans', function (Blueprint $table) {
                $table->dropColumn('tanggal_pengembalian_baru');
            });
        }
    }
};
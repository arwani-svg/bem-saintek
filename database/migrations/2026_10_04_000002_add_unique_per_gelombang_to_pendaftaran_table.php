<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan: Schema::table mengeksekusi SQL setelah closure selesai,
        // jadi try/catch harus membungkus seluruh pemanggilan, bukan isi closure.
        try {
            Schema::table('pendaftaran_pengurus', function (Blueprint $table) {
                // Hapus unik global NIM agar user bisa daftar lagi di periode berikutnya.
                $table->dropUnique(['nim']);
            });
        } catch (Throwable) {
            // Index mungkin bernama lain / sudah dihapus — abaikan.
        }

        // Kolom gelombang_id dibuat oleh migrasi gelombang yang tanggalnya
        // lebih baru; pada migrate fresh kolom belum ada di titik ini.
        // Pembuatan unik komposit ditangani migrasi ensure setelahnya.
        if (! Schema::hasColumn('pendaftaran_pengurus', 'gelombang_id')) {
            return;
        }

        try {
            Schema::table('pendaftaran_pengurus', function (Blueprint $table) {
                // Satu akun = satu pendaftaran per gelombang. Satu NIM = satu per gelombang.
                $table->unique(['gelombang_id', 'user_id'], 'pendaftaran_gelombang_user_unique');
                $table->unique(['gelombang_id', 'nim'], 'pendaftaran_gelombang_nim_unique');
            });
        } catch (Throwable) {
            // Sudah ada — abaikan.
        }
    }

    public function down(): void
    {
        Schema::table('pendaftaran_pengurus', function (Blueprint $table) {
            $table->dropUnique('pendaftaran_gelombang_user_unique');
            $table->dropUnique('pendaftaran_gelombang_nim_unique');
            $table->unique('nim');
        });
    }
};

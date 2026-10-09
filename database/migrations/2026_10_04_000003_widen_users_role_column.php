<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom legacy `role` (enum mahasiswa/pengurus) dilonggarkan menjadi
     * string agar bisa menampung jabatan baru (ketua-bem, bendahara, ...).
     * Sumber kebenaran hak akses adalah Spatie (tabel roles); kolom ini
     * hanya dipertahankan sebagai cache/sinkronisasi untuk kompatibilitas.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_new')->default('mahasiswa');
        });

        DB::table('users')->update(['role_new' => DB::raw('role')]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_new', 'role');
        });
    }

    public function down(): void
    {
        // Kembalikan ke enum semula (data di luar 2 nilai akan gagal).
        DB::table('users')->whereNotIn('role', ['mahasiswa', 'pengurus'])
            ->update(['role' => 'mahasiswa']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role_old')->default('mahasiswa');
        });

        DB::table('users')->update(['role_old' => DB::raw('role')]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_old', 'role');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memastikan unik komposit pendaftaran per gelombang ada.
     * Idempotent: aman dijalankan ulang di semua environment.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('pendaftaran_pengurus', 'gelombang_id')) {
            return;
        }

        try {
            Schema::table('pendaftaran_pengurus', function (Blueprint $table) {
                $table->dropUnique(['nim']);
            });
        } catch (Throwable) {
        }

        try {
            Schema::table('pendaftaran_pengurus', function (Blueprint $table) {
                $table->unique(['gelombang_id', 'user_id'], 'pendaftaran_gelombang_user_unique');
                $table->unique(['gelombang_id', 'nim'], 'pendaftaran_gelombang_nim_unique');
            });
        } catch (Throwable) {
        }
    }

    public function down(): void
    {
        // Sengaja no-op: unik komposit adalah pengaman anti-dobel.
    }
};

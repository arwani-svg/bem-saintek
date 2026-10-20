<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foto profil user — nullable agar default sama seperti sekarang
     * (user lama/tanpa foto tetap valid, frontend fallback ke inisial).
     * Disimpan sebagai path relatif di disk `public` (mis. foto-profil/xxx.jpg)
     * agar hemat storage hosting murah.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('nim');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};

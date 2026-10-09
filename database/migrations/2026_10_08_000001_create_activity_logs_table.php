<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log aktivitas penting saja (login/logout, create, update, delete,
     * pendaftaran, perubahan status). BUKAN log setiap request/halaman
     * agar tabel tetap ringan.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('activity', 50)->index();
            $table->string('description', 500)->nullable();
            // Referensi opsional ke record terkait (mis. transaksi, user).
            $table->nullableMorphs('subject');
            // Data tambahan yang sudah disaring (tanpa password/token).
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            // Filter umum halaman log: jenis + waktu, dan user + waktu.
            $table->index(['activity', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

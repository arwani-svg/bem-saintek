<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_pengurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama');
            $table->string('nim', 30)->unique();
            $table->string('prodi');
            $table->unsignedTinyInteger('semester');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('asrama')->nullable();
            $table->string('email')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('riwayat_sekolah')->nullable();
            $table->text('riwayat_madrasah')->nullable();
            $table->text('riwayat_organisasi')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->text('catatan_seleksi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_pengurus');
    }
};

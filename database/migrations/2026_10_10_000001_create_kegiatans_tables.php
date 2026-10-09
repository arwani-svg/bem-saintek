<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->date('tanggal')->index();
            $table->string('waktu_mulai', 5)->nullable(); // HH:MM
            $table->string('lokasi', 150)->nullable();
            // Poin pelanggaran untuk status alpa di kegiatan ini.
            $table->unsignedInteger('poin_alpa')->default(10);
            // terjadwal | berlangsung | selesai | dibatalkan
            $table->string('status', 20)->default('terjadwal')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('kegiatan_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['kegiatan_id', 'user_id']);
            $table->index('kegiatan_id');
        });

        Schema::create('kehadirans', function (Blueprint $table) {
            $table->id();
            // Satu baris final per peserta per kegiatan (upsert tiap ketukan keyboard).
            $table->foreignId('kegiatan_peserta_id')->unique()->constrained('kegiatan_pesertas')->cascadeOnDelete();
            // hadir | izin | sakit | alpa
            $table->string('status', 20)->index();
            $table->string('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
        Schema::dropIfExists('kegiatan_pesertas');
        Schema::dropIfExists('kegiatans');
    }
};

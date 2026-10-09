<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iurans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            // Nominal wajib bayar per peserta (rupiah). Bisa dioverride per peserta.
            $table->unsignedBigInteger('nominal');
            $table->date('jatuh_tempo')->nullable()->index();
            // aktif | selesai | dibatalkan
            $table->string('status', 20)->default('aktif')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('iuran_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iuran_id')->constrained('iurans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Salinan nominal saat peserta ditambahkan (boleh beda per orang).
            $table->unsignedBigInteger('nominal_wajib');
            $table->timestamps();

            $table->unique(['iuran_id', 'user_id']);
            $table->index('iuran_id');
        });

        Schema::create('iuran_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iuran_peserta_id')->constrained('iuran_pesertas')->cascadeOnDelete();
            $table->unsignedBigInteger('jumlah');
            $table->date('tanggal')->index();
            $table->string('keterangan')->nullable();
            // Link otomatis ke kas agar saldo tetap satu sumber kebenaran.
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi_keuangans')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_pembayarans');
        Schema::dropIfExists('iuran_pesertas');
        Schema::dropIfExists('iurans');
    }
};

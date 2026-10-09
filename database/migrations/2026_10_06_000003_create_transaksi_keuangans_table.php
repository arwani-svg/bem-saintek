<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_keuangans', function (Blueprint $table) {
            $table->id();
            // Sengaja string (bukan enum DB) agar tipe & kategori fleksibel
            // dan diatur di kode (App\Models\TransaksiKeuangan).
            $table->string('tipe', 20)->index(); // masuk | keluar
            $table->string('kategori', 100)->index();
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('jumlah'); // rupiah
            $table->date('tanggal')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_keuangans');
    }
};

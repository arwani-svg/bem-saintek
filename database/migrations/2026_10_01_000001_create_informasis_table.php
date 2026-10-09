<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informasis', function (Blueprint $table) {
            $table->id();
            // Satu tabel fleksibel untuk: berita, infografis, penghargaan, lomba
            $table->string('jenis', 20); // berita | infografis | penghargaan | lomba
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable(); // caption singkat (wajib utk infografis/lomba, excerpt utk berita/penghargaan)
            $table->longText('konten')->nullable(); // narasi panjang (dipakai berita & penghargaan)
            $table->string('gambar')->nullable(); // path di disk public, cth: informasi/xxx.jpg
            $table->date('tanggal_publikasi')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_unggulan')->default(false);
            $table->timestamps();

            $table->index('jenis');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasis');
    }
};

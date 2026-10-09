<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departemen_id')->constrained('departemens')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable(); // cth: Akademik, Sosial (kosong = ikut nama departemen)
            $table->string('warna', 20)->default('blue'); // blue|green|purple|orange|pink|indigo
            $table->string('status', 20)->default('berlangsung'); // akan_datang|berlangsung|selesai
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->boolean('is_unggulan')->default(true); // tampil di halaman depan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_kerjas');
    }
};

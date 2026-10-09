<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gelombang_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });

        Schema::table('pendaftaran_pengurus', function (Blueprint $table) {
            $table->foreignId('gelombang_id')->nullable()->after('user_id')
                ->constrained('gelombang_pendaftaran')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_pengurus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gelombang_id');
        });
        Schema::dropIfExists('gelombang_pendaftaran');
    }
};

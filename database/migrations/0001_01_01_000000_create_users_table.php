<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nim')->unique();
            $table->string('password');
            $table->enum('role', ['mahasiswa', 'pengurus'])->default('mahasiswa');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->enum('prodi', [
                'Teknologi Informasi',
                'Sistem Informasi',
                'Ilmu Komputer',
                'Arsitektur',
                'Budidaya Perikanan',
                'Teknologi Hasil Perikanan',
            ]);
            $table->integer('angkatan');
            $table->enum('kelas', ['A1', 'B1', 'C1', 'C2'])->nullable();
            $table->enum('status', ['aktif', 'cuti', 'DO', 'keluar', 'lulus'])->default('aktif');
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->text('kata_motivasi')->nullable();
            $table->text('prestasi')->nullable();
            $table->boolean('privasi')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

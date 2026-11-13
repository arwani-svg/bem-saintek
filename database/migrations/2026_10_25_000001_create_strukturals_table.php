<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strukturals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Null = Pengurus Harian (PH), bukan bagian dari departemen mana pun.
            $table->foreignId('departemen_id')->nullable()->constrained('departemens')->nullOnDelete();
            $table->string('jabatan', 100);
            $table->string('periode', 20)->index();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'periode', 'jabatan']);
            $table->index(['periode', 'departemen_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strukturals');
    }
};

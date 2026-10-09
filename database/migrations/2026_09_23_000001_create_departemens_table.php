<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // cth: PSDM
            $table->string('kepanjangan')->nullable(); // cth: Pengembangan SDM
            $table->text('deskripsi')->nullable();
            $table->string('icon', 10)->default('🏛️');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};

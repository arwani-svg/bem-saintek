<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departemens', function (Blueprint $table) {
            $table->text('tujuan')->nullable()->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('departemens', function (Blueprint $table) {
            $table->dropColumn('tujuan');
        });
    }
};

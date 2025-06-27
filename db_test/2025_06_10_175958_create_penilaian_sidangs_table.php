<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('penilaian_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_sidang_id');
            $table->foreignId('penguji_sidang_id');
            $table->string('aspek_penilaian');
            $table->decimal('nilai', 5, 2);
            $table->decimal('bobot', 3, 2);
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_sidangs');
    }
};

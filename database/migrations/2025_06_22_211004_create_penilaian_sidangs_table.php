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
        Schema::create('penilaian_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_sidang_id')->constrained('jadwal_sidangs')->onDelete('cascade');
            $table->foreignId('penguji_sidang_id')->constrained('penguji_sidangs')->onDelete('cascade');

            // 1. MATERI SKRIPSI (bobot 0.5)
            $table->integer('originalitas_materi')->nullable();
            $table->integer('analisa_metodologi')->nullable();
            $table->integer('tingkat_aplikasi_materi')->nullable();
            $table->integer('pengembangan_kreativitas')->nullable();
            $table->integer('tata_tulis')->nullable();

            // 2. PENYAJIAN (bobot 0.3)
            $table->integer('penguasaan_materi')->nullable();
            $table->integer('sikap_dan_penampilan')->nullable();
            $table->integer('penyajian_sarana_sistematika')->nullable();

            // 3. DISKUSI DAN TANYA JAWAB (bobot 0.2)
            $table->integer('hasil_yang_dicapai')->nullable();
            $table->integer('penguasaan_materi_diskusi')->nullable();
            $table->integer('objektivitas_tanggapan')->nullable();
            $table->integer('kemampuan_mempertahankan_ide')->nullable();

            // Perhitungan nilai
            $table->float('nilai_rata_rata')->nullable(); // 0-100
            $table->float('nilai_akhir')->nullable(); // nilai * bobot

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

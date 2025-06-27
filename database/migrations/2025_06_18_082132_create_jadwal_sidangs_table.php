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
        Schema::create('jadwal_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_tugas_akhir_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_sidang', ['proposal', 'skripsi', 'tugas akhir']);
            $table->string('file_sidang');
            $table->dateTime('tanggal_sidang');
            $table->string('tempat_sidang');
            $table->string('ruang_sidang')->nullable();
            $table->enum('status', ['dijadwalkan', 'berlangsung', 'selesai', 'ditunda', 'dibatalkan']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_sidangs');
    }
};

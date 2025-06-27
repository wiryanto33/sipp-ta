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
        Schema::create('pengajuan_tugas_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->string('judul');
            $table->text('sinopsis')->nullable();
            $table->string('bidang_penelitian')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'diterima', 'ditolak', 'sedang_bimbingan', 'siap_sidang', 'lulus', 'tidak_lulus'])->default('draft');
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_acc')->nullable();
            $table->string('file_proposal')->nullable();
            $table->string('file_skripsi')->nullable();
            $table->text('message')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_tugas_akhirs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
//     - id (bigint, primary key)
// - jadwal_sidang_id (foreign key)
// - nilai_akhir (decimal(5,2))
// - grade (enum: 'A', 'B+', 'B', 'C+', 'C', 'D', 'E')
// - status_kelulusan (enum: 'lulus', 'lulus_dengan_perbaikan', 'tidak_lulus')
// - catatan_perbaikan (text, nullable)
// - batas_waktu_perbaikan (date, nullable)
// - berita_acara (string, nullable) // file berita acara
// - created_at, updated_at (timestamps)
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_sidang_id');
            $table->decimal('nilai_akhir', 5, 2);
            $table->enum('grade', ['A', 'B+', 'B', 'C+', 'C', 'D', 'E']);
            $table->enum('status_kelulusan', ['lulus', 'lulus_dengan_perbaikan', 'tidak_lulus']);
            $table->text('catatan_perbaikan')->nullable();
            $table->date('batas_waktu_perbaikan')->nullable();
            $table->string('berita_acara')->nullable(); //file berita acara
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_sidangs');
    }
};

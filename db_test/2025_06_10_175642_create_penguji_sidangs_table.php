<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tabel penguji sidang
// - id (bigint, primary key)
// - jadwal_sidang_id (foreign key)
// - dosen_id (foreign key)
// - peran (enum: 'ketua', 'sekretaris', 'penguji_1', 'penguji_2', 'pembimbing_1', 'pembimbing_2')
// - hadir (boolean, default false)
// - created_at, updated_at (timestamps)
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penguji_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_sidang_id')->constrained()->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained()->onDelete('cascade');
            $table->enum('peran', ['ketua', 'sekretaris', 'penguji_1', 'penguji_2', 'pembimbing_1', 'pembimbing_2']);
            $table->boolean('hadir')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penguji_sidangs');
    }
};

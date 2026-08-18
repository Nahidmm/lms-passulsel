<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 60)->unique();      // e.g. kelola_pelatihan
            $table->string('nama', 150);               // e.g. Kelola Pelatihan
            $table->text('deskripsi')->nullable();
            $table->string('grup', 60)->default('Umum'); // Konten, Evaluasi, Pengguna, dll
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

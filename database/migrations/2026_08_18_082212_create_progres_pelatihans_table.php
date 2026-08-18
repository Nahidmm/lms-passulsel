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
        Schema::create('progres_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pelatihan_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();

            // Ensure a user can only have one progress record per pelatihan
            $table->unique(['user_id', 'pelatihan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres_pelatihans');
    }
};

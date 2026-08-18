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
        Schema::create('dokumen_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_ai_id')->constrained('dokumen_ais')->onDelete('cascade');
            $table->text('chunk_text');
            $table->json('embedding'); // Store vector array as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_chunks');
    }
};

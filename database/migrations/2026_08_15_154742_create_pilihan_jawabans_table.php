<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilihan_jawabans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('soal_id');
            $table->string('huruf', 1); // A, B, C, D
            $table->text('teks');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('soal_id')->references('id')->on('soals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilihan_jawabans');
    }
};

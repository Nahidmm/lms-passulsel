<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->enum('tipe', ['pilgan', 'esai'])->default('pilgan');
            $table->unsignedBigInteger('materi_id');
            $table->text('pembahasan')->nullable(); // penjelasan jawaban
            $table->unsignedInteger('bobot')->default(10); // poin per soal
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('materi_id')->references('id')->on('materis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};

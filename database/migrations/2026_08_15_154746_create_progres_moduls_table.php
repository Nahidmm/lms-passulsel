<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_moduls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('materi_id');
            $table->enum('status', ['belum', 'sedang', 'selesai'])->default('belum');
            $table->unsignedInteger('persen')->default(0); // 0–100
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'materi_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('materi_id')->references('id')->on('materis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_moduls');
    }
};

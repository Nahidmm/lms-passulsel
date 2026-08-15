<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_latihans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sesi_evaluasi_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('soal_id');
            $table->unsignedBigInteger('pilihan_id')->nullable(); // for pilgan
            $table->text('jawaban_esai')->nullable();             // for esai
            $table->boolean('is_correct')->nullable();
            $table->unsignedInteger('skor')->default(0);
            $table->timestamps();

            $table->foreign('sesi_evaluasi_id')->references('id')->on('sesi_evaluasis')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('soal_id')->references('id')->on('soals')->onDelete('cascade');
            $table->foreign('pilihan_id')->references('id')->on('pilihan_jawabans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_latihans');
    }
};

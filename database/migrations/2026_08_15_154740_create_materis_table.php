<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->enum('jenis', ['pdf', 'ppt', 'pptx', 'link', 'video_embed', 'quiz'])->default('pdf');
            $table->string('file_path', 500)->nullable();
            $table->string('url_link', 500)->nullable();
            $table->unsignedBigInteger('modul_id');
            $table->unsignedInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('durasi_baca')->default(10); // menit estimasi

            // Gating / Prerequisites
            $table->unsignedBigInteger('prasyarat_materi_id')->nullable();

            // Quiz Settings (Only relevant if jenis = 'quiz')
            $table->unsignedInteger('passing_grade')->default(70);
            $table->unsignedInteger('durasi_menit')->nullable(); // null = unlimited
            $table->unsignedInteger('max_attempts')->default(0); // 0 = unlimited
            $table->boolean('acak_soal')->default(false);
            $table->boolean('acak_jawaban')->default(false);
            $table->boolean('tampilkan_feedback')->default(true);
            $table->boolean('strict_anti_cheat')->default(false);

            $table->timestamps();

            $table->foreign('modul_id')->references('id')->on('moduls')->onDelete('cascade');
            $table->foreign('prasyarat_materi_id')->references('id')->on('materis')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};

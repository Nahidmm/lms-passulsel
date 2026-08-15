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
            $table->enum('jenis', ['pdf', 'ppt', 'pptx', 'link', 'video_embed'])->default('pdf');
            $table->string('file_path', 500)->nullable();
            $table->string('url_link', 500)->nullable();
            $table->unsignedBigInteger('jabatan_id');
            $table->unsignedInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('durasi_baca')->default(10); // menit estimasi
            $table->timestamps();

            $table->foreign('jabatan_id')->references('id')->on('jabatans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};

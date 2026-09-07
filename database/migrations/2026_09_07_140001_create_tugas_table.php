<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['tugas_umum', 'upload_sertifikat'])->default('tugas_umum');
            $table->string('file_lampiran')->nullable(); // Template/panduan dari admin
            $table->dateTime('deadline')->nullable();
            $table->decimal('bobot_nilai', 5, 2)->default(100);
            $table->string('format_file_diizinkan')->default('pdf,docx,jpg,png,zip');
            $table->integer('max_file_size_mb')->default(10);
            $table->integer('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};

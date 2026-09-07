<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_nama_asli');
            $table->text('catatan_peserta')->nullable();
            // Khusus tipe upload_sertifikat
            $table->string('nomor_sertifikat')->nullable();
            $table->date('tanggal_sertifikat')->nullable();
            $table->string('penyelenggara')->nullable();
            // Penilaian
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('feedback_instruktur')->nullable();
            $table->enum('status', ['submitted', 'need_revision', 'graded'])->default('submitted');
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('dinilai_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->timestamps();

            $table->unique(['tugas_id', 'user_id']); // 1 submission per tugas per peserta
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_submissions');
    }
};

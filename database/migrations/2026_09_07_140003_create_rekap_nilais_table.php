<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pelatihan_id')->constrained()->cascadeOnDelete();
            $table->decimal('nilai_pretest', 5, 2)->default(0);
            $table->decimal('nilai_quiz_rata', 5, 2)->default(0);
            $table->decimal('nilai_tugas', 5, 2)->default(0);
            $table->decimal('nilai_posttest', 5, 2)->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->string('predikat')->nullable(); // A, B, C, D
            $table->enum('status_kelulusan', ['lulus', 'tidak_lulus', 'belum'])->default('belum');
            $table->timestamps();

            $table->unique(['user_id', 'pelatihan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_nilais');
    }
};

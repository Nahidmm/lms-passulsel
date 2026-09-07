<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel konfigurasi bobot nilai yang bisa diubah admin per pelatihan
        Schema::create('bobot_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained()->cascadeOnDelete();
            $table->decimal('bobot_pretest', 5, 2)->default(10.00);   // 10%
            $table->decimal('bobot_quiz', 5, 2)->default(20.00);      // 20%
            $table->decimal('bobot_tugas', 5, 2)->default(35.00);     // 35%
            $table->decimal('bobot_posttest', 5, 2)->default(35.00);  // 35%
            $table->decimal('passing_grade', 5, 2)->default(65.00);   // Batas kelulusan
            $table->timestamps();

            $table->unique('pelatihan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_nilais');
    }
};

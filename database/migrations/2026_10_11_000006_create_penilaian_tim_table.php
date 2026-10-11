<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_tim', function (Blueprint $table) {
            $table->id();
            $table->string('tim', 30); // mental | fisik | akhlak | kompetensi
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // peserta
            $table->unsignedInteger('pekan')->default(1);
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tim', 'user_id', 'pekan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_tim');
    }
};

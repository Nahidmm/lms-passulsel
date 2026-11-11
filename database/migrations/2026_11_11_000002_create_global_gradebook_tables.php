<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gradebook_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->default('global');
            $table->decimal('bobot_quiz', 5, 2)->default(20);
            $table->decimal('bobot_tugas', 5, 2)->default(40);
            $table->decimal('bobot_mental', 5, 2)->default(10);
            $table->decimal('bobot_fisik', 5, 2)->default(10);
            $table->decimal('bobot_akhlak', 5, 2)->default(10);
            $table->decimal('bobot_kompetensi', 5, 2)->default(10);
            $table->decimal('passing_grade', 5, 2)->default(65);
            $table->timestamps();
        });

        Schema::create('rekap_nilai_globals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('nilai_quiz', 5, 2)->nullable();
            $table->decimal('nilai_tugas', 5, 2)->nullable();
            $table->decimal('nilai_mental', 5, 2)->nullable();
            $table->decimal('nilai_fisik', 5, 2)->nullable();
            $table->decimal('nilai_akhlak', 5, 2)->nullable();
            $table->decimal('nilai_kompetensi', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->string('predikat')->nullable();
            $table->string('status_kelulusan')->default('belum_lengkap');
            $table->unsignedInteger('item_selesai')->default(0);
            $table->unsignedInteger('total_item')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_nilai_globals');
        Schema::dropIfExists('gradebook_settings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_evaluasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('jabatan_id');
            $table->enum('status', ['berlangsung', 'selesai', 'timeout'])->default('berlangsung');
            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->unsignedInteger('durasi_menit')->default(30);
            $table->unsignedInteger('total_soal')->default(0);
            $table->unsignedInteger('benar')->default(0);
            $table->decimal('skor', 5, 2)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jabatan_id')->references('id')->on('jabatans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_evaluasis');
    }
};

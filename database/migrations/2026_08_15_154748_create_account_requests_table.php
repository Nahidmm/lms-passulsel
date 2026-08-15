<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->unique();
            $table->string('nama', 150);
            $table->string('email', 150)->nullable();
            $table->string('golongan', 10)->nullable();
            $table->unsignedBigInteger('jabatan_id')->nullable();
            $table->text('pesan')->nullable(); // alasan pendaftaran
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->unsignedBigInteger('diproses_oleh')->nullable(); // admin user_id
            $table->string('alasan_tolak', 500)->nullable();
            $table->timestamp('tanggal_proses')->nullable();
            $table->timestamps();

            $table->foreign('jabatan_id')->references('id')->on('jabatans')->onDelete('set null');
            $table->foreign('diproses_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_requests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->unsignedBigInteger('pelatihan_id')->nullable()->after('id');
            $table->dropForeign(['modul_id']);
            $table->dropColumn('modul_id');
        });

        Schema::dropIfExists('progres_moduls');
        Schema::dropIfExists('moduls');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('moduls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelatihan_id')->nullable();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('progres_moduls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('modul_id');
            $table->enum('status', ['belum', 'sedang', 'selesai'])->default('belum');
            $table->integer('persen')->default(0);
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
        });

        Schema::table('materis', function (Blueprint $table) {
            $table->dropColumn('pelatihan_id');
            $table->unsignedBigInteger('modul_id')->nullable()->after('id');
        });
    }
};

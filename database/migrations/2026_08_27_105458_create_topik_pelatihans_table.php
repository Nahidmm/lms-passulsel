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
        Schema::create('topik_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_topik');
            $table->unsignedInteger('batas_nilai')->default(70);
            $table->unsignedBigInteger('pelatihan_id')->nullable();
            $table->timestamps();

            $table->foreign('pelatihan_id')->references('id')->on('pelatihans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topik_pelatihans');
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_taken_pretest')->default(false)->after('status_akun');
        });

        Schema::table('materis', function (Blueprint $table) {
            $table->boolean('is_pretest')->default(false)->after('jenis');
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->unsignedBigInteger('topik_pelatihan_id')->nullable()->after('bobot');
            $table->foreign('topik_pelatihan_id')->references('id')->on('topik_pelatihans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_taken_pretest');
        });

        Schema::table('materis', function (Blueprint $table) {
            $table->dropColumn('is_pretest');
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->dropForeign(['topik_pelatihan_id']);
            $table->dropColumn('topik_pelatihan_id');
        });
    }
};

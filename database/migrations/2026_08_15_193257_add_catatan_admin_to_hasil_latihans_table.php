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
        Schema::table('hasil_latihans', function (Blueprint $table) {
            $table->text('catatan_admin')->nullable()->after('jawaban_esai');
            $table->decimal('skor', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_latihans', function (Blueprint $table) {
            $table->dropColumn('catatan_admin');
            $table->unsignedInteger('skor')->default(0)->change();
        });
    }
};

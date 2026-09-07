<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekap_nilais', function (Blueprint $table) {
            $table->decimal('nilai_pretest', 5, 2)->nullable()->default(null)->change();
            $table->decimal('nilai_quiz_rata', 5, 2)->nullable()->default(null)->change();
            $table->decimal('nilai_tugas', 5, 2)->nullable()->default(null)->change();
            $table->decimal('nilai_posttest', 5, 2)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('rekap_nilais', function (Blueprint $table) {
            $table->decimal('nilai_pretest', 5, 2)->default(0)->change();
            $table->decimal('nilai_quiz_rata', 5, 2)->default(0)->change();
            $table->decimal('nilai_tugas', 5, 2)->default(0)->change();
            $table->decimal('nilai_posttest', 5, 2)->default(0)->change();
        });
    }
};

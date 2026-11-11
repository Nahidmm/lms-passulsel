<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bobot_nilais', function (Blueprint $table) {
            $table->decimal('bobot_mental', 5, 2)->default(10)->after('bobot_posttest');
            $table->decimal('bobot_fisik', 5, 2)->default(10)->after('bobot_mental');
            $table->decimal('bobot_akhlak', 5, 2)->default(10)->after('bobot_fisik');
            $table->decimal('bobot_kompetensi', 5, 2)->default(10)->after('bobot_akhlak');
        });

        DB::table('bobot_nilais')->update([
            'bobot_pretest' => 0,
            'bobot_quiz' => 20,
            'bobot_tugas' => 40,
            'bobot_posttest' => 0,
            'bobot_mental' => 10,
            'bobot_fisik' => 10,
            'bobot_akhlak' => 10,
            'bobot_kompetensi' => 10,
        ]);
    }

    public function down(): void
    {
        Schema::table('bobot_nilais', function (Blueprint $table) {
            $table->dropColumn(['bobot_mental', 'bobot_fisik', 'bobot_akhlak', 'bobot_kompetensi']);
        });
    }
};

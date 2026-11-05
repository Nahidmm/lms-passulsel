<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            if (!Schema::hasColumn('pelatihans', 'sertif_aktif')) {
                $table->boolean('sertif_aktif')->default(true)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            if (Schema::hasColumn('pelatihans', 'sertif_aktif')) {
                $table->dropColumn('sertif_aktif');
            }
        });
    }
};

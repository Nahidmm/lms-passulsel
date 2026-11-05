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
        Schema::table('sertifikat_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('sertifikat_settings', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('tipe_ttd');
            }
            if (!Schema::hasColumn('sertifikat_settings', 'generation_mode')) {
                $table->string('generation_mode')->default('admin_only')->after('is_active'); // 'auto' or 'admin_only'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikat_settings', function (Blueprint $table) {
            if (Schema::hasColumn('sertifikat_settings', 'generation_mode')) {
                $table->dropColumn('generation_mode');
            }
            if (Schema::hasColumn('sertifikat_settings', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};

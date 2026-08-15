<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE soals MODIFY tipe VARCHAR(50) DEFAULT 'pilihan_ganda'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE soals MODIFY tipe ENUM('pilihan_ganda', 'multi_select', 'isian_singkat', 'essay') DEFAULT 'pilihan_ganda'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// This migration is no longer needed as fields are in the create_users_table migration.
// Kept as empty stub to avoid migration order issues.
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};

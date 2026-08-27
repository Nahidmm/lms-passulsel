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
            $table->string('mode_tampilan')->default('standard')->after('strict_anti_cheat');
            $table->string('sub_mode')->default('standard')->after('mode_tampilan');
            $table->unsignedInteger('timer_per_soal')->default(0)->after('sub_mode');
            $table->boolean('sound_enabled')->default(true)->after('timer_per_soal');
            $table->boolean('leaderboard_enabled')->default(false)->after('sound_enabled');
            $table->boolean('bonus_kecepatan_enabled')->default(false)->after('leaderboard_enabled');
            $table->boolean('animasi_enabled')->default(true)->after('bonus_kecepatan_enabled');
            $table->boolean('badge_enabled')->default(false)->after('animasi_enabled');
            $table->boolean('show_answer_review')->default(true)->after('badge_enabled');
            $table->string('theme_name')->nullable()->after('show_answer_review');
        });

        Schema::table('sesi_evaluasis', function (Blueprint $table) {
            $table->unsignedInteger('current_soal_index')->default(0)->after('total_soal');
            $table->json('jawaban_tersimpan')->nullable()->after('current_soal_index');
            $table->dateTime('waktu_mulai_soal')->nullable()->after('jawaban_tersimpan');
            $table->dateTime('waktu_terakhir_aksi')->nullable()->after('waktu_mulai_soal');
            $table->unsignedInteger('streak')->default(0)->after('waktu_terakhir_aksi');
            $table->unsignedInteger('xp_earned')->default(0)->after('streak');
            $table->boolean('is_paused')->default(false)->after('xp_earned');
            $table->dateTime('last_activity_at')->nullable()->after('is_paused');
            $table->unsignedInteger('tab_blur_count')->default(0)->after('last_activity_at');
        });

        Schema::table('hasil_latihans', function (Blueprint $table) {
            $table->boolean('is_skipped')->default(false)->after('is_correct');
            $table->unsignedInteger('response_time_seconds')->default(0)->after('is_skipped');
            $table->json('answer_order')->nullable()->after('response_time_seconds');
            $table->boolean('feedback_shown')->default(false)->after('answer_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropColumn([
                'mode_tampilan',
                'sub_mode',
                'timer_per_soal',
                'sound_enabled',
                'leaderboard_enabled',
                'bonus_kecepatan_enabled',
                'animasi_enabled',
                'badge_enabled',
                'show_answer_review',
                'theme_name',
            ]);
        });

        Schema::table('sesi_evaluasis', function (Blueprint $table) {
            $table->dropColumn([
                'current_soal_index',
                'jawaban_tersimpan',
                'waktu_mulai_soal',
                'waktu_terakhir_aksi',
                'streak',
                'xp_earned',
                'is_paused',
                'last_activity_at',
                'tab_blur_count',
            ]);
        });

        Schema::table('hasil_latihans', function (Blueprint $table) {
            $table->dropColumn([
                'is_skipped',
                'response_time_seconds',
                'answer_order',
                'feedback_shown',
            ]);
        });
    }
};

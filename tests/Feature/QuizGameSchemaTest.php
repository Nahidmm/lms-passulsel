<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuizGameSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_quiz_game_columns_exist_on_materi_table(): void
    {
        $this->assertTrue(Schema::hasColumn('materis', 'mode_tampilan'));
        $this->assertTrue(Schema::hasColumn('materis', 'sub_mode'));
        $this->assertTrue(Schema::hasColumn('materis', 'timer_per_soal'));
        $this->assertTrue(Schema::hasColumn('materis', 'sound_enabled'));
        $this->assertTrue(Schema::hasColumn('materis', 'leaderboard_enabled'));
        $this->assertTrue(Schema::hasColumn('materis', 'bonus_kecepatan_enabled'));
        $this->assertTrue(Schema::hasColumn('materis', 'animasi_enabled'));
        $this->assertTrue(Schema::hasColumn('materis', 'badge_enabled'));
        $this->assertTrue(Schema::hasColumn('materis', 'show_answer_review'));
        $this->assertTrue(Schema::hasColumn('materis', 'theme_name'));
    }
}

<?php

namespace Tests\Unit;

use App\Services\GlobalGradebookService;
use PHPUnit\Framework\TestCase;

class GlobalGradebookServiceTest extends TestCase
{
    public function test_it_applies_global_weights_once_to_the_final_grade(): void
    {
        $result = GlobalGradebookService::calculateWeightedFinal(
            [
                'quiz' => 80,
                'tugas' => 90,
                'mental' => 85,
                'fisik' => 80,
                'akhlak' => 90,
                'kompetensi' => 88,
            ],
            [
                'quiz' => 20,
                'tugas' => 40,
                'mental' => 10,
                'fisik' => 10,
                'akhlak' => 10,
                'kompetensi' => 10,
            ],
            array_fill_keys(['quiz', 'tugas', 'mental', 'fisik', 'akhlak', 'kompetensi'], true)
        );

        $this->assertSame(86.3, $result['final']);
        $this->assertSame(16.0, $result['contributions']['quiz']);
        $this->assertSame(36.0, $result['contributions']['tugas']);
    }

    public function test_it_normalizes_only_categories_that_exist(): void
    {
        $result = GlobalGradebookService::calculateWeightedFinal(
            [
                'quiz' => null,
                'tugas' => 80,
                'mental' => 80,
                'fisik' => 80,
                'akhlak' => 80,
                'kompetensi' => 80,
            ],
            [
                'quiz' => 20,
                'tugas' => 40,
                'mental' => 10,
                'fisik' => 10,
                'akhlak' => 10,
                'kompetensi' => 10,
            ],
            [
                'quiz' => false,
                'tugas' => true,
                'mental' => true,
                'fisik' => true,
                'akhlak' => true,
                'kompetensi' => true,
            ]
        );

        $this->assertSame(80.0, $result['final']);
        $this->assertSame(0.0, $result['effective_weights']['quiz']);
        $this->assertSame(50.0, $result['effective_weights']['tugas']);
    }

    public function test_missing_required_score_contributes_zero_until_completed(): void
    {
        $result = GlobalGradebookService::calculateWeightedFinal(
            ['quiz' => null, 'tugas' => 100],
            ['quiz' => 50, 'tugas' => 50],
            ['quiz' => true, 'tugas' => true]
        );

        $this->assertSame(50.0, $result['final']);
        $this->assertSame(0.0, $result['contributions']['quiz']);
        $this->assertSame(50.0, $result['contributions']['tugas']);
    }
}

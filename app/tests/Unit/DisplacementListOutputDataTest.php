<?php

namespace Tests\Unit\Application\UseCases\DisplacementList;

use App\Application\UseCases\DisplacementList\DisplacementListOutputData;
use App\Domain\DisplacementList\Entities\Displacement;
use PHPUnit\Framework\TestCase;

class DisplacementListOutputDataTest extends TestCase
{
    public function test_toArrayが正しい構造を返す(): void
    {
        $displacements = [
            new Displacement(1, '660cc以下', 0.0, 660.0, false),
        ];

        $outputData = new DisplacementListOutputData($displacements);
        $result = $outputData->toArray();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('DisplacementList', $result['data']);
        $this->assertArrayHasKey('count', $result['data']);
        $this->assertSame(1, $result['data']['count']);
    }

    public function test_空配列で初期化した場合countが0になる(): void
    {
        $outputData = new DisplacementListOutputData([]);
        $result = $outputData->toArray();

        $this->assertSame(0, $result['data']['count']);
        $this->assertSame([], $result['data']['DisplacementList']);
    }
}
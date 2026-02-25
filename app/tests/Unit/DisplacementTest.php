<?php

namespace Tests\Unit\Domain\DisplacementList\Entities;

use App\Domain\DisplacementList\Entities\Displacement;
use PHPUnit\Framework\TestCase;

class DisplacementTest extends TestCase
{
    public function test_正常にインスタンスが生成できる(): void
    {
        $displacement = new Displacement(
            id: 1,
            name: '660cc以下',
            minAmount: 0.0,
            maxAmount: 660.0,
            isUnlimited: false,
        );

        $this->assertSame(1, $displacement->getId());
        $this->assertSame('660cc以下', $displacement->getName());
        $this->assertSame(0.0, $displacement->getMinAmount());
        $this->assertSame(660.0, $displacement->getMaxAmount());
        $this->assertFalse($displacement->getIsUnlimited());
    }

    public function test_nullableなフィールドにnullを渡せる(): void
    {
        $displacement = new Displacement(
            id: 1,
            name: null,
            minAmount: null,
            maxAmount: null,
            isUnlimited: true,
        );

        $this->assertNull($displacement->getName());
        $this->assertNull($displacement->getMinAmount());
        $this->assertNull($displacement->getMaxAmount());
        $this->assertTrue($displacement->getIsUnlimited());
    }

    public function test_toArrayが正しいキーと値を返す(): void
    {
        $displacement = new Displacement(
            id: 1,
            name: '660cc以下',
            minAmount: 0.0,
            maxAmount: 660.0,
            isUnlimited: false,
        );

        $result = $displacement->toArray();

        $this->assertSame([
            'id'           => 1,
            'name'         => '660cc以下',
            'min_amount'   => 0.0,
            'max_amount'   => 660.0,
            'is_unlimited' => false,
        ], $result);
    }

    public function test_toArrayでnullの場合デフォルト値が返る(): void
    {
        $displacement = new Displacement(
            id: 1,
            name: null,
            minAmount: null,
            maxAmount: null,
            isUnlimited: true,
        );

        $result = $displacement->toArray();

        $this->assertSame('', $result['name']);
        $this->assertSame(0, $result['min_amount']);
        $this->assertSame(0, $result['max_amount']);
    }
}
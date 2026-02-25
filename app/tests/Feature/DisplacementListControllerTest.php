<?php

namespace Tests\Feature\DisplacementList;

use App\Domain\DisplacementList\Repositories\DisplacementRepositoryInterface;
use App\Domain\DisplacementList\Entities\Displacement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplacementListControllerTest extends TestCase
{
    public function test_正常に排気量一覧が取得できる(): void
    {
        $displacements = [
            new Displacement(1, '660cc以下', 0.0, 660.0, false),
            new Displacement(2, '661cc〜1000cc', 661.0, 1000.0, false),
        ];

        $this->mock(DisplacementRepositoryInterface::class, function ($mock) use ($displacements) {
            $mock->shouldReceive('findActive')->once()->andReturn($displacements);
        });

        $response = $this->getJson('/api/Displacements');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'count' => 2,
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'DisplacementList' => [
                        '*' => [
                            'id',
                            'name',
                            'min_amount',
                            'max_amount',
                            'is_unlimited',
                        ],
                    ],
                    'count',
                ],
            ]);
    }

    public function test_空配列の場合も正常にレスポンスが返る(): void
    {
        $this->mock(DisplacementRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('findActive')->once()->andReturn([]);
        });

        $response = $this->getJson('/api/Displacements');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'DisplacementList' => [],
                    'count'            => 0,
                ],
            ]);
    }

    public function test_リポジトリで例外が発生した場合500が返る(): void
    {
        $this->mock(DisplacementRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('findActive')->once()->andThrow(new \Exception('DB error'));
        });

        $response = $this->getJson('/api/Displacements');

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ]);
    }
}

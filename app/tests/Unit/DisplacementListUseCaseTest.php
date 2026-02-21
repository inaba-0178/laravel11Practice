<?php

namespace Tests\Unit\Application\UseCases\DisplacementList;

use App\Application\UseCases\DisplacementList\DisplacementListUseCase;
use App\Application\UseCases\DisplacementList\DisplacementListOutputData;
use App\Domain\DisplacementList\Entities\Displacement;
use App\Domain\DisplacementList\Repositories\DisplacementRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Exception;

class DisplacementListUseCaseTest extends TestCase
{
    public function test_正常に排気量一覧が取得できる(): void
    {
        $displacements = [
            new Displacement(1, '660cc以下', 0.0, 660.0, false),
            new Displacement(2, '661cc〜1000cc', 661.0, 1000.0, false),
        ];

        $repository = $this->createMock(DisplacementRepositoryInterface::class);
        $repository->method('findActive')->willReturn($displacements);

        $useCase = new DisplacementListUseCase($repository);
        $result = $useCase->execute();

        $this->assertInstanceOf(DisplacementListOutputData::class, $result);
        $this->assertCount(2, $result->toArray()['data']['DisplacementList']);
    }

    public function test_リポジトリで例外が発生した場合に例外がスローされる(): void
    {
        $repository = $this->createMock(DisplacementRepositoryInterface::class);
        $repository->method('findActive')->willThrowException(new Exception('DB error'));

        $useCase = new DisplacementListUseCase($repository);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('排気量一覧の取得に失敗しました');

        $useCase->execute();
    }

    public function test_空配列が返された場合も正常に動作する(): void
    {
        $repository = $this->createMock(DisplacementRepositoryInterface::class);
        $repository->method('findActive')->willReturn([]);

        $useCase = new DisplacementListUseCase($repository);
        $result = $useCase->execute();

        $array = $result->toArray();
        $this->assertSame(true, $array['success']);
        $this->assertSame([], $array['data']['DisplacementList']);
        $this->assertSame(0, $array['data']['count']);
    }
}
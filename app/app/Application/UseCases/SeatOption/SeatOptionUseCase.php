<?php
namespace App\Application\UseCases\SeatOption;

use App\Domain\SeatOption\Repositories\SeatOptionRepositoryInterface;

class SeatOptionUseCase
{
    public function __construct(
        private readonly SeatOptionRepositoryInterface $repository,
    ) {}

    public function handle(): SeatOptionOutputData
    {
        $SeatOption = $this->repository->getAll();
        return new SeatOptionOutputData($SeatOption);
    }
}
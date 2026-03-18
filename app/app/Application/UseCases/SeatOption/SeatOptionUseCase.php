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
        $seatOption = $this->repository->getAll();
        return new SeatOptionOutputData($seatOption);
    }
}
<?php
namespace App\Application\UseCases\CarTypeOption;

use App\Domain\CarTypeOption\Repositories\CarTypeOptionRepositoryInterface;

class CarTypeOptionUseCase
{
    public function __construct(
        private readonly CarTypeOptionRepositoryInterface $repository,
    ) {}
    public function handle(): CarTypeOptionOutputData
    {
        $carTypeOption = $this->repository->getAll();
        return new CarTypeOptionOutputData($carTypeOption);
    }
}
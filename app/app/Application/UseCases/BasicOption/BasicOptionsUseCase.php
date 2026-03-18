<?php

namespace App\Application\UseCases\BasicOption;

use App\Domain\BasicOption\Repositories\BasicOptionsRepositoryInterface;

class BasicOptionsUseCase
{
    public function __construct(
        private readonly BasicOptionsRepositoryInterface $repository,
    ) {}

    public function handle(): BasicOptionsOutputData
    {
        $basicOptions = $this->repository->getAll();

        return new BasicOptionsOutputData($basicOptions);
    }
}
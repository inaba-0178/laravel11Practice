<?php

namespace App\Application\UseCases\DetailOption;

use App\Domain\DetailOption\Repositories\DetailOptionsRepositoryInterface;

class DetailOptionsUseCase
{
    public function __construct(
        private readonly DetailOptionsRepositoryInterface $repository,
    ) {}

    public function handle(): DetailOptionsOutputData
    {
        $detailOptions = $this->repository->getAll();

        return new DetailOptionsOutputData($detailOptions);
    }
}
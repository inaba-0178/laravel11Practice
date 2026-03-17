<?php

namespace App\Application\UseCases\ColorOption;

use App\Domain\ColorOption\Repositories\ColorOptionsRepositoryInterface;

class ColorOptionsUseCase
{
    public function __construct(
        private readonly ColorOptionsRepositoryInterface $repository,
    ) {}

    public function handle(): ColorOptionsOutputData
    {
        $colorOptions = $this->repository->getAll();

        return new ColorOptionsOutputData($colorOptions);
    }
}
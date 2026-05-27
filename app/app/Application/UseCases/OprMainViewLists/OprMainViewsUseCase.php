<?php

namespace App\Application\UseCases\OprMainViewLists;

use App\Domain\OprMainViewLists\Repositories\OprMainViewRepositoryInterface;

class OprMainViewsUseCase
{
    public function __construct(
        private readonly OprMainViewRepositoryInterface $repository
    ) {}

    public function execute(): OprMainViewsOutputData
    {
        $views = $this->repository->findActive();
        return new OprMainViewsOutputData($views);
    }
}
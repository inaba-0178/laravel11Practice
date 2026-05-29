<?php

namespace App\Application\UseCases\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\ManufacturerListAllRepositoryInterface;

class ManufacturerListAllUseCase
{
    public function __construct(
        private readonly ManufacturerListAllRepositoryInterface $repository
    ) {}

    public function execute(): ManufacturerListAllOutputData
    {
        $groups = $this->repository->findGrouped();
        return new ManufacturerListAllOutputData($groups);
    }
}
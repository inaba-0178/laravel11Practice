<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerAffiliatedData;

use App\Domain\SelectDealerAffiliatedData\Repositories\AffiliatedStoreRepositoryInterface;

final class SelectDealerAffiliatedDataUseCase
{
    public function __construct(
        private readonly AffiliatedStoreRepositoryInterface $repository,
    ) {}

    public function execute(SelectDealerAffiliatedDataInputData $input): SelectDealerAffiliatedDataOutputData
    {
        $stores = $this->repository->findByDealerId($input->dealerId);

        return new SelectDealerAffiliatedDataOutputData($stores);
    }
}
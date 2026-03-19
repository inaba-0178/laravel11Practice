<?php

namespace App\Application\UseCases\SelectDealerData;

use App\Domain\SelectDealerData\Repositories\DealerRepositoryInterface;

class SelectDealerDataUseCase
{
    public function __construct(
        private readonly DealerRepositoryInterface $dealerRepository,
    ) {}

    public function execute(int $dealerId): SelectDealerDataOutputData
    {
        $dealer = $this->dealerRepository->findById($dealerId);

        return new SelectDealerDataOutputData($dealer);
    }
}
<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerInfoData;

use App\Domain\SelectDealerInfoData\Repositories\DealerRepositoryInterface;
use App\Domain\SelectDealerInfoData\Repositories\DealerImageRepositoryInterface;
use App\Domain\SelectDealerInfoData\Exceptions\DealerNotFoundException;

final class SelectDealerInfoDataUseCase
{
    public function __construct(
        private readonly DealerRepositoryInterface      $dealerRepository,
        private readonly DealerImageRepositoryInterface $dealerImageRepository,
    ) {}

    /**
     * @throws DealerNotFoundException
     */
    public function execute(SelectDealerInfoDataInputData $input): SelectDealerInfoDataOutputData
    {
        $dealerId = $input->dealerId->getValue();

        $dealer = $this->dealerRepository->findById($dealerId);
        $images = $this->dealerImageRepository->findByDealerId($dealerId);

        return new SelectDealerInfoDataOutputData($dealer, $images);
    }
}
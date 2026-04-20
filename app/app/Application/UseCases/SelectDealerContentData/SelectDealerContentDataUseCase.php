<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerContentData;

use App\Domain\SelectDealerContentData\Repositories\DealerContentRepositoryInterface;

final class SelectDealerContentDataUseCase
{
    public function __construct(
        private readonly DealerContentRepositoryInterface $contentRepository,
    ) {}

    public function execute(SelectDealerContentDataInputData $input): SelectDealerContentDataOutputData
    {
        $contents = $this->contentRepository->findByDealerId($input->dealerId);

        return new SelectDealerContentDataOutputData($contents);
    }
}
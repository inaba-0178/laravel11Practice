<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerStaffData;

use App\Domain\SelectDealerStaffData\Repositories\DealerStaffRepositoryInterface;

final class SelectDealerStaffDataUseCase
{
    public function __construct(
        private readonly DealerStaffRepositoryInterface $staffRepository,
    ) {}

    public function execute(SelectDealerStaffDataInputData $input): SelectDealerStaffDataOutputData
    {
        $staffs = $this->staffRepository->findByDealerId($input->dealerId);

        return new SelectDealerStaffDataOutputData($staffs);
    }
}
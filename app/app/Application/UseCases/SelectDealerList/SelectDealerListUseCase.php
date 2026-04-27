<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerList;

use App\Domain\SelectDealerList\Repositories\DealerListRepositoryInterface;

final class SelectDealerListUseCase
{
    public function __construct(
        private readonly DealerListRepositoryInterface $repository,
    ) {}

    public function execute(SelectDealerListInputData $input): SelectDealerListOutputData
    {
        $dealers    = $this->repository->findByCondition($input->condition);
        $totalCount = $this->repository->countByCondition($input->condition);

        return new SelectDealerListOutputData(
            dealers     : $dealers,
            totalCount  : $totalCount,
            currentPage : $input->condition->page,
            perPage     : $input->condition->perPage,
        );
    }
}
<?php
namespace App\Application\UseCases\LoanMonthlyOption;

use App\Domain\LoanMonthlyOption\Repositories\LoanMonthlyOptionRepositoryInterface;

class LoanMonthlyOptionUseCase
{
    public function __construct(
        private readonly LoanMonthlyOptionRepositoryInterface $repository,
    ) {}
    public function handle(): LoanMonthlyOptionOutputData
    {
        $loanMonthlyOption = $this->repository->getAll();
        return new LoanMonthlyOptionOutputData($loanMonthlyOption);
    }
}
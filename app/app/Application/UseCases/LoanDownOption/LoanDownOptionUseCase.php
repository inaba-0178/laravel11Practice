<?php
namespace App\Application\UseCases\LoanDownOption;

use App\Domain\LoanDownOption\Repositories\LoanDownOptionRepositoryInterface;

class LoanDownOptionUseCase
{
    public function __construct(
        private readonly LoanDownOptionRepositoryInterface $repository,
    ) {}
    public function handle(): LoanDownOptionOutputData
    {
        $loanDownOption = $this->repository->getAll();
        return new LoanDownOptionOutputData($loanDownOption);
    }
}
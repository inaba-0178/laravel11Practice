<?php
namespace App\Application\UseCases\LoanMonthlyOption;

use Illuminate\Support\Collection;
use App\Domain\LoanMonthlyOption\Entities\LoanMonthlyOption;

class LoanMonthlyOptionOutputData
{
    public function __construct(
        public readonly Collection $loanMonthlyOptions,
    ) {}
    public function toArray(): array
    {
        return $this->loanMonthlyOptions
            ->map(fn(LoanMonthlyOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}

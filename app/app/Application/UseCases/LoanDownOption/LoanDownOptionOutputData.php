<?php
namespace App\Application\UseCases\LoanDownOption;

use Illuminate\Support\Collection;
use App\Domain\LoanDownOption\Entities\LoanDownOption;

class LoanDownOptionOutputData
{
    public function __construct(
        public readonly Collection $loanDownOptions,
    ) {}
    public function toArray(): array
    {
        return $this->loanDownOptions
            ->map(fn(LoanDownOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}

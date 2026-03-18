<?php

namespace App\Infrastructure\Repositories\LoanDownOption;

use App\Domain\LoanDownOption\Repositories\LoanDownOptionRepositoryInterface;
use App\Infrastructure\Eloquent\Opr\OprLoanDownOption;
use App\Domain\LoanDownOption\Entities\LoanDownOption;
use Illuminate\Support\Collection;

class EloquentLoanDownOptionRepository implements LoanDownOptionRepositoryInterface
{
    public function getAll(): Collection
    {
        return OprLoanDownOption::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => new LoanDownOption(
                id:             $model->id,
                value:          $model->value,
                label:          $model->label,
                sortOrder:      $model->sort_order,
                isActive:       $model->is_active,
            ));
    }
}
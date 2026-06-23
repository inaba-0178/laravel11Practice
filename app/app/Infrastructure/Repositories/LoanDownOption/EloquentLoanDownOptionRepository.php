<?php

namespace App\Infrastructure\Repositories\LoanDownOption;

use App\Domain\LoanDownOption\Repositories\LoanDownOptionRepositoryInterface;
use App\Infrastructure\Eloquent\Opr\OprLoanDownOption;
use App\Domain\LoanDownOption\Entities\LoanDownOption;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentLoanDownOptionRepository implements LoanDownOptionRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_LOAN_DOWN_OPTIONS, CacheConstants::TTL_MST, function () {
            return OprLoanDownOption::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new LoanDownOption(
                    id:        $model->id,
                    value:     $model->value,
                    label:     $model->label,
                    sortOrder: $model->sort_order,
                    isActive:  $model->is_active,
                ));
        });
    }
}

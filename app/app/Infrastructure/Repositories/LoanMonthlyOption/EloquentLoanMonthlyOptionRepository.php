<?php

namespace App\Infrastructure\Repositories\LoanMonthlyOption;

use App\Domain\LoanMonthlyOption\Repositories\LoanMonthlyOptionRepositoryInterface;
use App\Infrastructure\Eloquent\Opr\OprLoanMonthlyOption;
use App\Domain\LoanMonthlyOption\Entities\LoanMonthlyOption;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentLoanMonthlyOptionRepository implements LoanMonthlyOptionRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_LOAN_MONTHLY_OPTIONS, CacheConstants::TTL_MST, function () {
            return OprLoanMonthlyOption::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new LoanMonthlyOption(
                    id:        $model->id,
                    value:     $model->value,
                    label:     $model->label,
                    sortOrder: $model->sort_order,
                    isActive:  $model->is_active,
                ));
        });
    }
}

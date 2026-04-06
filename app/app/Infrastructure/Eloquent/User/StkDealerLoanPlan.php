<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkDealerLoanPlan extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_loan_plans';

    protected $fillable = [
        'dealer_id',
        'name',
        'interest_rate',
        'months_options',
        'min_months',
        'max_months',
        'bonus_amount',
        'bonus_times',
        'is_active',
        'delete_requested_by',
        'delete_request_reason',
        'delete_requested_at',
        'delete_approved_by',
        'delete_approved_at',
    ];

    protected $casts = [
        'interest_rate'       => 'decimal:2',
        'months_options'      => 'array',
        'bonus_amount'        => 'decimal:0',
        'is_active'           => 'boolean',
        'delete_requested_at' => 'datetime',
        'delete_approved_at'  => 'datetime',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function calcMonthlyPayment(int $price, int $months, int $downPayment = 0): float
    {
        $principal   = $price - $downPayment;
        $monthlyRate = ($this->interest_rate / 100) / 12;

        if ($monthlyRate == 0) {
            $monthly = $principal / $months;
        } else {
            $monthly = $principal * $monthlyRate * pow(1 + $monthlyRate, $months)
                / (pow(1 + $monthlyRate, $months) - 1);
        }

        if ($this->bonus_amount && $this->bonus_times) {
            $totalBonus = $this->bonus_amount * $this->bonus_times;
            $monthly   -= $totalBonus / $months;
        }

        return round($monthly);
    }
}
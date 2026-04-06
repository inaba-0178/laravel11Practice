<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstLoanPlan extends Model
{
    protected $connection = 'mst';
    protected $table      = 'mst_loan_plans';

    protected $fillable = [
        'name',
        'interest_rate',
        'months_options',
        'min_months',
        'max_months',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'interest_rate'  => 'decimal:2',
        'months_options' => 'array',
        'is_default'     => 'boolean',
        'is_active'      => 'boolean',
    ];

    // デフォルトプランを取得
    public static function getDefault(): ?self
    {
        return static::where('is_default', 1)
            ->where('is_active', 1)
            ->first();
    }

    // 月々支払い計算
    public function calcMonthlyPayment(int $price, int $months, int $downPayment = 0, int $bonusAmount = 0, int $bonusTimes = 0): float
    {
        $principal   = $price - $downPayment;
        $monthlyRate = ($this->interest_rate / 100) / 12;

        if ($monthlyRate == 0) {
            $monthly = $principal / $months;
        } else {
            $monthly = $principal * $monthlyRate * pow(1 + $monthlyRate, $months)
                / (pow(1 + $monthlyRate, $months) - 1);
        }

        // ボーナス払い分を月々から差し引き
        if ($bonusAmount > 0 && $bonusTimes > 0) {
            $totalBonus  = $bonusAmount * $bonusTimes;
            $monthly    -= $totalBonus / $months;
        }

        return round($monthly);
    }
}
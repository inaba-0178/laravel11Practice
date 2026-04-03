<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StkCarLoan extends Model
{
    protected $connection = 'user';
    protected $table      = 'stk_car_loans';

    protected $fillable = [
        'car_id',
        'loan_type',
        'interest_rate',
        'loan_months',
        'down_payment',
        'residual_value',
        'misc_fee',
        'note',
    ];

    protected $casts = [
        'interest_rate'  => 'decimal:2',
        'down_payment'   => 'decimal:0',
        'residual_value' => 'decimal:0',
        'misc_fee'       => 'decimal:0',
    ];

    // ローンタイプ定数
    const TYPE_STANDARD = 'standard';
    const TYPE_RESIDUAL = 'residual';

    const TYPE_LABELS = [
        self::TYPE_STANDARD => '通常ローン',
        self::TYPE_RESIDUAL => '残価設定ローン',
    ];

    // システムデフォルト金利
    const DEFAULT_INTEREST_RATE = 3.9;
    const DEFAULT_LOAN_MONTHS   = 60;

    public function car(): BelongsTo
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }

    // ===== 月々支払い額を計算 =====
    public function calcMonthlyPayment(int $price): ?float
    {
        $rate     = $this->interest_rate ?? self::DEFAULT_INTEREST_RATE;
        $months   = $this->loan_months   ?? self::DEFAULT_LOAN_MONTHS;
        $down     = $this->down_payment  ?? 0;
        $residual = $this->loan_type === self::TYPE_RESIDUAL ? ($this->residual_value ?? 0) : 0;

        $principal = $price - $down - $residual;
        if ($principal <= 0) return 0;

        $monthlyRate = ($rate / 100) / 12;

        if ($monthlyRate == 0) {
            return $principal / $months;
        }

        return $principal * $monthlyRate * pow(1 + $monthlyRate, $months)
            / (pow(1 + $monthlyRate, $months) - 1);
    }
}
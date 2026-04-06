<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Infrastructure\Eloquent\Mst\MstLoanPlan;

class StkCarLoan extends Model
{
    protected $connection = 'user';
    protected $table      = 'stk_car_loans';

    protected $fillable = [
        'car_id',
        'loan_type',
        'dealer_loan_plan_id',
        // スナップショット
        'snapshot_plan_name',
        'snapshot_rate',
        'snapshot_months_options',
        'snapshot_min_months',
        'snapshot_max_months',
        'snapshot_bonus_amount',
        'snapshot_bonus_times',
        // 契約確定
        'is_contracted',
        'contracted_at',
        'contracted_plan_name',
        'contracted_rate',
        'contracted_months',
        'contracted_monthly_amount',
        'contracted_bonus_amount',
        'contracted_bonus_times',
        // 既存
        'interest_rate',
        'loan_months',
        'down_payment',
        'residual_value',
        'misc_fee',
        'note',
    ];

    protected $casts = [
        'interest_rate'           => 'decimal:2',
        'down_payment'            => 'decimal:0',
        'residual_value'          => 'decimal:0',
        'misc_fee'                => 'decimal:0',
        'snapshot_rate'           => 'decimal:2',
        'snapshot_months_options' => 'array',
        'snapshot_bonus_amount'   => 'decimal:0',
        'contracted_rate'         => 'decimal:2',
        'contracted_monthly_amount' => 'decimal:0',
        'contracted_bonus_amount' => 'decimal:0',
        'is_contracted'           => 'boolean',
        'contracted_at'           => 'datetime',
    ];

    const TYPE_STANDARD = 'standard';
    const TYPE_RESIDUAL = 'residual';

    const TYPE_LABELS = [
        self::TYPE_STANDARD => '通常ローン',
        self::TYPE_RESIDUAL => '残価設定ローン',
    ];

    const DEFAULT_INTEREST_RATE = 3.9;
    const DEFAULT_LOAN_MONTHS   = 60;

    public function car(): BelongsTo
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }

    public function dealerLoanPlan(): BelongsTo
    {
        return $this->belongsTo(StkDealerLoanPlan::class, 'dealer_loan_plan_id');
    }

    // ===== ディーラープランからスナップショットを作成 =====
    public function takeSnapshot(StkDealerLoanPlan $plan): void
    {
        $this->update([
            'dealer_loan_plan_id'     => $plan->id,
            'snapshot_plan_name'      => $plan->name,
            'snapshot_rate'           => $plan->interest_rate,
            'snapshot_months_options' => $plan->months_options,
            'snapshot_min_months'     => $plan->min_months,
            'snapshot_max_months'     => $plan->max_months,
            'snapshot_bonus_amount'   => $plan->bonus_amount,
            'snapshot_bonus_times'    => $plan->bonus_times,
        ]);
    }

    // ===== 契約確定時にローン情報をロック =====
    public function lockAsContracted(int $months, float $monthlyAmount): void
    {
        $this->update([
            'is_contracted'              => 1,
            'contracted_at'              => now(),
            'contracted_plan_name'       => $this->snapshot_plan_name ?? 'システムデフォルト',
            'contracted_rate'            => $this->getEffectiveRate(),
            'contracted_months'          => $months,
            'contracted_monthly_amount'  => $monthlyAmount,
            'contracted_bonus_amount'    => $this->snapshot_bonus_amount,
            'contracted_bonus_times'     => $this->snapshot_bonus_times,
        ]);
    }

    // ===== 有効な金利を取得（優先順位に従って） =====
    public function getEffectiveRate(): float
    {
        // 契約確定済み → 契約時の金利
        if ($this->is_contracted) {
            return (float) $this->contracted_rate;
        }

        // スナップショットあり → スナップショットの金利
        if ($this->snapshot_rate) {
            return (float) $this->snapshot_rate;
        }

        // ディーラー設定あり → ディーラー設定の金利
        if ($this->interest_rate) {
            return (float) $this->interest_rate;
        }

        // mst_loan_plans のデフォルト
        $defaultPlan = MstLoanPlan::getDefault();
        if ($defaultPlan) {
            return (float) $defaultPlan->interest_rate;
        }

        // システムデフォルト
        return self::DEFAULT_INTEREST_RATE;
    }

    // ===== 有効な回数選択肢を取得 =====
    public function getEffectiveMonthsOptions(): array
    {
        if ($this->is_contracted) {
            return [$this->contracted_months];
        }

        if ($this->snapshot_months_options) {
            return $this->snapshot_months_options;
        }

        $defaultPlan = MstLoanPlan::getDefault();
        if ($defaultPlan) {
            return $defaultPlan->months_options;
        }

        return [12, 24, 36, 48, 60, 120];
    }

    // ===== 月々支払い計算 =====
    public function calcMonthlyPayment(int $price, int $months): float
    {
        // 契約確定済みの場合は契約時の月々支払額を返す
        if ($this->is_contracted) {
            return (float) $this->contracted_monthly_amount;
        }

        $rate      = $this->getEffectiveRate();
        $down      = (float) ($this->down_payment ?? 0);
        $residual  = $this->loan_type === self::TYPE_RESIDUAL ? (float) ($this->residual_value ?? 0) : 0;
        $principal = $price - $down - $residual;

        if ($principal <= 0) return 0;

        $monthlyRate = ($rate / 100) / 12;

        if ($monthlyRate == 0) {
            $monthly = $principal / $months;
        } else {
            $monthly = $principal * $monthlyRate * pow(1 + $monthlyRate, $months)
                / (pow(1 + $monthlyRate, $months) - 1);
        }

        // ボーナス払い分を差し引き
        $bonusAmount = (float) ($this->snapshot_bonus_amount ?? 0);
        $bonusTimes  = (int) ($this->snapshot_bonus_times ?? 0);
        if ($bonusAmount > 0 && $bonusTimes > 0) {
            $monthly -= ($bonusAmount * $bonusTimes) / $months;
        }

        return round($monthly);
    }
}
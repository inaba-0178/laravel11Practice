<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * 見積Eloquentモデル
 *
 * stk_estimatesテーブルのEloquentモデル。
 * 見積データの保存・PDF管理を担当する。
 */
class StkEstimate extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_estimates';

    protected $fillable = [
        'estimate_number',
        'dealer_id',
        'car_id',
        'inquiry_id',
        'customer_name',
        'customer_nickname',
        'customer_phone',
        'customer_postal_code',
        'customer_address',
        'customer_birth_date',
        'customer_workplace',
        'customer_contact_phone',
        'vehicle_price',
        'discount',
        'discount_type',
        'recycle_fee',
        'weight_tax',
        'liability_insurance',
        'vehicle_tax',
        'registration_fee',
        'garage_cert_fee',
        'delivery_fee',
        'maintenance_fee',
        'environmental_performance_tax',
        'inspection_registration_fee',
        'inspection_registration_fee_exempt',
        'trade_in_handling_fee',
        'assessment_fee',
        'vehicle_model',
        'chassis_number',
        'registration_number',
        'has_service_record',
        'trade_in_name',
        'trade_in_model_year',
        'trade_in_inspection_date',
        'trade_in_mileage',
        'trade_in_color',
        'trade_in_price',
        'down_payment',
        'remaining_amount',
        'credit_months',
        'credit_fee',
        'monthly_payment',
        'bonus_payment',
        'accessories',
        'documents',
        'notes',
        'pdf_path',
        'valid_until',
        'created_by',
    ];

    protected $casts = [
        'accessories'                           => 'array',
        'documents'                             => 'array',
        'valid_until'                           => 'date',
        'customer_birth_date'                   => 'date',
        'trade_in_inspection_date'              => 'date',
        'has_service_record'                    => 'boolean',
        'vehicle_price'                         => 'integer',
        'discount'                              => 'integer',
        'recycle_fee'                           => 'integer',
        'weight_tax'                            => 'integer',
        'liability_insurance'                   => 'integer',
        'vehicle_tax'                           => 'integer',
        'registration_fee'                      => 'integer',
        'garage_cert_fee'                       => 'integer',
        'delivery_fee'                          => 'integer',
        'maintenance_fee'                       => 'integer',
        'environmental_performance_tax'         => 'integer',
        'inspection_registration_fee'           => 'integer',
        'inspection_registration_fee_exempt'    => 'integer',
        'trade_in_handling_fee'                 => 'integer',
        'assessment_fee'                        => 'integer',
        'trade_in_price'                        => 'integer',
        'down_payment'                          => 'integer',
        'remaining_amount'                      => 'integer',
        'credit_months'                         => 'integer',
        'credit_fee'                            => 'integer',
        'monthly_payment'                       => 'integer',
        'bonus_payment'                         => 'integer',
        'trade_in_mileage'                      => 'integer',
    ];

    /** ディーラー */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    /** 車両 */
    public function car(): BelongsTo
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }

    /** 問い合わせ */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(StkInquiry::class, 'inquiry_id');
    }

    /**
     * 見積番号を自動採番する
     * 例：EST-20260605-0001
     */
    public static function generateEstimateNumber(): string
    {
        $prefix = 'EST-' . now()->format('Ymd') . '-';
        $latest = static::where('estimate_number', 'like', $prefix . '%')
            ->orderByDesc('estimate_number')
            ->value('estimate_number');

        $sequence = $latest
            ? (int) substr($latest, -4) + 1
            : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * 車両本体価格（税込）
     */
    public function getPriceWithTaxAttribute(): int
    {
        return (int) round($this->vehicle_price * 1.1);
    }

    /**
     * 値引き後本体価格
     */
    public function getDiscountedPriceAttribute(): int
    {
        return $this->vehicle_price - $this->discount;
    }

    /**
     * 諸費用合計
     */
    public function getMiscFeesTotalAttribute(): int
    {
        return $this->recycle_fee
            + $this->weight_tax
            + $this->liability_insurance
            + $this->vehicle_tax
            + $this->registration_fee
            + $this->garage_cert_fee
            + $this->delivery_fee
            + $this->maintenance_fee;
    }

    /**
     * 支払総額
     */
    public function getTotalPriceAttribute(): int
    {
        return $this->getPriceWithTaxAttribute() + $this->misc_fees_total;
    }

    /** 担当者 */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
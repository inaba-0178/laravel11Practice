<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class StkCarDealer extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_car_dealers';

    protected $fillable = [
        'name',
        'postal_code',
        'region_id',
        'city',
        'address_detail',
        'phone',
        'business_hours',
        'regular_holiday',
        'latitude',
        'longitude',
        'review_rating',
        'review_count',
        'is_active',
    ];

    protected $casts = [
        'region_id'     => 'integer',
        'latitude'      => 'float',
        'longitude'     => 'float',
        'review_rating' => 'float',
        'review_count'  => 'integer',
        'is_active'     => 'boolean',
    ];


    // ===== StkCarDealer モデルに以下のリレーションを追加 =====
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'loan_setting_requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'loan_setting_approved_by');
    }

    // ===== ディーラー側でローン設定が使えるかチェック =====
    public function canSetLoan(): bool
    {
        return $this->loan_setting_enabled == 1;
    }

    // ===== 申請中かどうか =====
    public function isLoanSettingPending(): bool
    {
        return !empty($this->loan_setting_requested_at)
            && $this->loan_setting_enabled == 0
            && empty($this->loan_setting_rejected_reason);
    }
}
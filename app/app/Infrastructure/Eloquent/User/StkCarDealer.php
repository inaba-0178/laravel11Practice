<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Infrastructure\Eloquent\User\StkDealerLoanPlan;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Constants\AffiliatedStoreStatus;

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
        'business_hours_from',
        'business_hours_to',
        'regular_holiday_days',
        'regular_holiday_except_holiday',
        'latitude',
        'longitude',
        'review_rating',
        'review_count',
        'is_active',
        'loan_setting_enabled',
        'loan_setting_requested_by',
        'loan_setting_reason',
        'loan_setting_requested_at',
        'loan_setting_approved_by',
        'loan_setting_approved_at',
        'loan_setting_rejected_reason',
        'email',
        'website_url',
        'area_code',
        'address_detail',
        'dealer_type',
        'is_maker_dealer',
        'free_text',
    ];

    protected $casts = [
        'region_id'                      => 'integer',
        'area_code'                      => 'integer',
        'latitude'                       => 'float',
        'longitude'                      => 'float',
        'review_rating'                  => 'float',
        'review_count'                   => 'integer',
        'is_active'                      => 'boolean',
        'regular_holiday_except_holiday' => 'boolean',
        'loan_setting_requested_at'      => 'datetime',
        'loan_setting_approved_at'       => 'datetime',
        'is_maker_dealer'                => 'boolean',
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

    // リレーション追加
    public function loanPlans(): HasMany
    {
        return $this->hasMany(StkDealerLoanPlan::class, 'dealer_id');
    }
    
    public function activeLoanPlans(): HasMany
    {
        return $this->hasMany(StkDealerLoanPlan::class, 'dealer_id')
            ->where('is_active', 1);
    }

    public function images(): HasMany
    {
        return $this->hasMany(StkDealerImage::class, 'dealer_id');
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(StkDealerImage::class, 'dealer_id')
            ->where('is_main', true);
    }

    public function staffs(): HasMany
    {
        return $this->hasMany(StkDealerStaff::class, 'dealer_id');
    }

    public function activeStaffs(): HasMany
    {
        return $this->hasMany(StkDealerStaff::class, 'dealer_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(StkDealerContent::class, 'dealer_id');
    }

    public function activeContents(): HasMany
    {
        return $this->hasMany(StkDealerContent::class, 'dealer_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // 自分が申請元の系列店・提携店
    public function affiliatedStores()
    {
        return $this->hasMany(StkAffiliatedStore::class, 'dealer_id')
            ->where('status', AffiliatedStoreStatus::APPROVED);
    }

    // 自分が申請先の系列店・提携店
    public function affiliatedByStores()
    {
        return $this->hasMany(StkAffiliatedStore::class, 'affiliated_dealer_id')
            ->where('status', AffiliatedStoreStatus::APPROVED);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(StkCar::class, 'dealer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(StkDealerReview::class, 'dealer_id')
            ->whereNull('deleted_at');
    }
}
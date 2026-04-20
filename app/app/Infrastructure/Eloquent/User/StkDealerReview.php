<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkDealerReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_reviews';

    protected $fillable = [
        'dealer_id',
        'member_id',
        'rating',
        'rating_service',
        'rating_atmosphere',
        'rating_after',
        'rating_quality',
        'comment',
        'purchased_car',
        'purchased_at',
    ];

    protected $casts = [
        'dealer_id'         => 'integer',
        'rating'            => 'integer',
        'rating_service'    => 'integer',
        'rating_atmosphere' => 'integer',
        'rating_after'      => 'integer',
        'rating_quality'    => 'integer',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function member()
    {
        return $this->belongsTo(UsrUser::class, 'member_id');
    }

    public function replies()
    {
        return $this->hasMany(StkDealerReviewReply::class, 'review_id');
    }

    public function activeReplies()
    {
        return $this->hasMany(StkDealerReviewReply::class, 'review_id')
            ->whereNull('deleted_at');
    }
}
<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkDealerReview extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_reviews';

    protected $fillable = [
        'dealer_id',
        'member_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'dealer_id' => 'integer',
        'member_id' => 'integer',
        'rating'    => 'integer',
    ];
}
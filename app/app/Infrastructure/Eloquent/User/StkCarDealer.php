<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
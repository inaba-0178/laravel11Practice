<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;

class StkDealerReservationTypes extends Model
{
    protected $connection = 'user';
    protected $table      = 'stk_dealer_reservation_types';

    protected $fillable = [
        'dealer_id',
        'reservation_type_id',
        'is_active',
    ];

    protected $casts = [
        'dealer_id'           => 'integer',
        'reservation_type_id' => 'integer',
        'is_active'           => 'integer',
    ];
}
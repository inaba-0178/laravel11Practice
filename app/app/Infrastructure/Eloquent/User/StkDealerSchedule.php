<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;

class StkDealerSchedule extends Model
{
    protected $connection = 'user';
    protected $table      = 'stk_dealer_schedules';

    protected $fillable = [
        'dealer_id',
        'reservation_type_id',
        'date',
        'time_from',
        'time_to',
        'max_reservations',
        'is_available',
    ];

    protected $casts = [
        'dealer_id'           => 'integer',
        'reservation_type_id' => 'integer',
        'max_reservations'    => 'integer',
        'is_available'        => 'integer',
    ];
}
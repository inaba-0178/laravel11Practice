<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkReservation extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_reservations';

    protected $fillable = [
        'dealer_id',
        'car_id',
        'member_id',
        'reservation_type_id',
        'schedule_id',
        'status',
        'memo',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_address',
    ];

    protected $casts = [
        'dealer_id'           => 'integer',
        'car_id'              => 'integer',
        'reservation_type_id' => 'integer',
        'schedule_id'         => 'integer',
    ];
}
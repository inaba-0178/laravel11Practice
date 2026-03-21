<?php

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;

class OprReservationTypes extends Model
{
    protected $connection = 'mst';
    protected $table      = 'opr_reservation_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'integer',
        'sort_order' => 'integer',
    ];
}
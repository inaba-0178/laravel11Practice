<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstVehicleWeightTax extends Model
{
    protected $connection = 'mst';
    protected $table      = 'mst_vehicle_weight_taxes';

    protected $fillable = [
        'weight_from',
        'weight_to',
        'is_light',
        'amount',
    ];

    protected $casts = [
        'is_light' => 'boolean',
        'amount'   => 'decimal:0',
    ];
}
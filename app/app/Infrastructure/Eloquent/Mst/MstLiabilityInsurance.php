<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstLiabilityInsurance extends Model
{
    protected $connection = 'mst';
    protected $table      = 'mst_liability_insurances';

    protected $fillable = [
        'vehicle_type',
        'months',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
    ];
}
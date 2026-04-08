<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkDealerFee extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_fees';

    protected $fillable = [
        'dealer_id',
        'name',
        'is_default',
        'registration_fee',
        'garage_cert_fee',
        'delivery_fee',
        'maintenance_fee',
    ];

    protected $casts = [
        'is_default'       => 'boolean',
        'registration_fee' => 'decimal:0',
        'garage_cert_fee'  => 'decimal:0',
        'delivery_fee'     => 'decimal:0',
        'maintenance_fee'  => 'decimal:0',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }
}
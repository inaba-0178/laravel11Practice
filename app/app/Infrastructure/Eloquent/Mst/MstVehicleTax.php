<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 自動車税マスタモデル
 */
class MstVehicleTax extends Model
{
    protected $connection = 'mst';
    protected $table      = 'mst_vehicle_taxes';

    protected $fillable = [
        'version_id',
        'displacement_list_id',
        'is_light',
        'amount',
    ];

    protected $casts = [
        'is_light' => 'boolean',
        'amount'   => 'integer',
    ];

    public function displacementList(): BelongsTo
    {
        return $this->belongsTo(MstDisplacementList::class, 'displacement_list_id');
    }
}
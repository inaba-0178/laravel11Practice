<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;

class MstVehicles extends Model
{
    use HasFactory;

    protected $connection = 'mst';
    protected $table = 'mst_vehicles';

    protected $fillable = [
        'series_id',
        'manufacturer_id',
        'name',
        'model_code',
        'body_type',
        'country_code',
        'status',
    ];

    public function carSeries(): BelongsTo
    {
        return $this->belongsTo(MstCarSeries::class, 'series_id', 'series_id');
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(MstBodyTypes::class, 'body_type');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(MstManufacturers::class, 'manufacturer_id');
    }

    public function yearVersions(): HasMany
    {
        return $this->hasMany(MstVehicleYearVersions::class, 'vehicle_id');
    }
}

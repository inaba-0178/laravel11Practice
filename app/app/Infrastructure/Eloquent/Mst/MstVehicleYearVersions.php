<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstVehicleYearVersions extends Model
{
    use HasFactory;

    protected $connection = 'mst';
    protected $table = 'mst_vehicle_year_versions';

    protected $fillable = [
        'vehicle_id',
        'year_from',
        'year_to',
        'displacement_cc',
        'drive_type',
        'fuel_efficiency',
        'max_power_kw',
        'transmission_type',
        'weight_kg',
        'price_range_from',
        'price_range_to',
        'is_latest',
    ];

    protected $casts = [
        'is_latest' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(MstVehicles::class, 'vehicle_id');
    }
}

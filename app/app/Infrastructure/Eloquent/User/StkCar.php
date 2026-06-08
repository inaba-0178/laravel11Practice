<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Eloquent\User\StkCarLoan;
use App\Infrastructure\Eloquent\User\StkDealerFee;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;

class StkCar extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table = 'stk_cars';

    protected $fillable = [
        'dealer_id',
        'manufacturer_id',
        'series_id',
        'vehicle_id',
        'year_version_id',
        'stock_number',
        'status',
        'price',
        'recycle_fee',
        'price_display_type',
        'model_year',
        'mileage',
        'body_type_id',
        'color',
        'color_group',
        'transmission',
        'fuel_type',
        'region_id',
        'repair_history',
        'main_image_url',
        'published_at',
        'publish_end_at',
        'sold_at',
        'rejection_reason',
        'dealer_fee_id',
        'bulk_upload_key',
        'bulk_batch_id',
    ];

    protected $casts = [
        'price'             => 'float',
        'recycle_fee'       => 'float',
        'model_year'        => 'integer',
        'mileage'           => 'integer',
        'published_at'      => 'datetime',
        'publish_end_at'    => 'datetime',
        'sold_at'           => 'datetime',
        'rejection_reason'  => 'array',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function detail()
    {
        return $this->hasOne(StkCarDetail::class, 'car_id');
    }

    public function images()
    {
        return $this->hasMany(StkCarImages::class, 'car_id');
    }

    public function options()
    {
        return $this->hasMany(StkCarOptions::class, 'car_id');
    }

    public function stats()
    {
        return $this->hasOne(StkCarStats::class, 'car_id');
    }

    public function series()
    {
        return $this->belongsTo(MstCarSeries::class, 'series_id', 'series_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(MstVehicles::class, 'vehicle_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(MstManufacturers::class, 'manufacturer_id');
    }

    public function loans()
    {
        return $this->hasMany(StkCarLoan::class, 'car_id');
    }

    public function dealerFee()
    {
        return $this->belongsTo(StkDealerFee::class, 'dealer_fee_id');
    }

    public function bodyType()
    {
        return $this->belongsTo(MstBodyTypes::class, 'body_type_id');
    }

    public function bulkBatch()
    {
        return $this->belongsTo(StkBulkUploadBatch::class, 'bulk_batch_id');
    }
}
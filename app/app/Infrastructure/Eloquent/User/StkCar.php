<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkCar extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table = 'stk_cars';

    protected $fillable = [
        'dealer_id',
        'series_id',
        'vehicle_id',
        'stock_number',
        'status',
        'price',
        'price_display_type',
        'model_year',
        'mileage',
        'body_type_id',
        'color',
        'transmission',
        'fuel_type',
        'region_id',
        'repair_history',
        'main_image_url',
        'published_at',
        'sold_at',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'published_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function detail()
    {
        return $this->hasOne(StkCarDetails::class, 'car_id');
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
}
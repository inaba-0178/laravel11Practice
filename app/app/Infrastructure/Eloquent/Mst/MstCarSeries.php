<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstCarSeries extends Model
{
    use HasFactory;

    protected   $connection = 'mst'; 
    protected   $table = 'mst_car_series';
    protected   $primaryKey = 'series_id';
    public      $incrementing = false;
    protected   $keyType = 'int';

    protected $fillable = [
        'series_id',
        'series_name',
        'manufacturer_id',
    ];

    public function mstCarSeriesBodyTypes()
    {
        return $this->hasMany(MstCarSeriesBodyTypes::class, 'series_id');
    }

    public function mstManufacturer()
    {
        return $this->hasOne(MstManufacturers::class, 'id', 'manufacturer_id');
    }
}

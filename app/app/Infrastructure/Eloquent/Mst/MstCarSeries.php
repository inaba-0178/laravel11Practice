<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstCarSeries extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_car_series';

    protected $fillable = [
        'series_id',
        'series_name',
        'manufacturer_id',
    ];

}

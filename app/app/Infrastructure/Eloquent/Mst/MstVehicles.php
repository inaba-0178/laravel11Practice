<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}

<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstCarSeriesBodyTypes extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_car_series_body_types';

    protected $fillable = [
        'id',
	    'series_id',
    	'body_type_id',
	    'is_primary',
	    'sort_order',
    ];

    public function mstBodyType()
    {
        return $this->hasOne(MstBodyTypes::class, 'id', 'body_type_id');
    }
}

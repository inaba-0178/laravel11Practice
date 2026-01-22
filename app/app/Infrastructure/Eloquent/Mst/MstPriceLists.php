<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstPriceLists extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_price_lists';

    protected $fillable = [
        'range_name',
        'max_amount',
        'is_unlimited',
    ];

    // protected $casts = [
    //     'area_code' => 'integer',
    //     'sort_order' => 'integer',
    // ];

}

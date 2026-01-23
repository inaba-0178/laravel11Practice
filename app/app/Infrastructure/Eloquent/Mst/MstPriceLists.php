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
        'name',
        'max_amount',
        'is_unlimited',
    ];
}

<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstMileageLists extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_c_lists';

    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'is_unlimited',
    ];
}

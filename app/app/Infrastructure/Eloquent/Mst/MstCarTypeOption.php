<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstCarTypeOption extends Model
{
    protected $connection = 'mst';
    protected $table = 'mst_car_type_options';

    protected $fillable = [
        'value',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order'    => 'integer',
        'is_active'     => 'boolean',
    ];
}
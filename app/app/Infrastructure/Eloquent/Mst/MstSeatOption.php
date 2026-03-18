<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstSeatOption extends Model
{
    protected $connection = 'mst';
    protected $table = 'mst_seat_options';

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
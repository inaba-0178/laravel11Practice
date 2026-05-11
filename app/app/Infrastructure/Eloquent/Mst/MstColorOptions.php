<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstColorOptions extends Model
{
    protected $connection = 'mst';
    protected $table = 'mst_color_options';

    protected $fillable = [
        'value',
        'label',
        'hex_code',
        'group',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];
}
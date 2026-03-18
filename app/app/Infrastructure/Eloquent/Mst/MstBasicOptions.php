<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstBasicOptions extends Model
{
    protected $connection = 'mst';
    protected $table = 'mst_basic_options';

    protected $fillable = [
        'value',
        'label',
        'is_highlight',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_highlight'  => 'boolean',
        'sort_order'    => 'integer',
        'is_active'     => 'boolean',
    ];
}
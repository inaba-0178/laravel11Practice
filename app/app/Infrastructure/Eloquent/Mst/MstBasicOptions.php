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
        'is_highlights',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];
}
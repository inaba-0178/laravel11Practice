<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

class MstCountries extends Model
{
    protected $connection = 'mst';
    protected $table      = 'mst_countries';

    protected $fillable = [
        'country_code',
        'label',
        'flag',
        'anchor',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'integer',
    ];
}
<?php

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OprMainView extends Model
{
    use SoftDeletes;

    protected $connection = 'mst';
    protected $table      = 'opr_main_views';

    protected $fillable = [
        'title',
        'sub',
        'label',
        'image_path',
        'link_url',
        'sort_order',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];
}
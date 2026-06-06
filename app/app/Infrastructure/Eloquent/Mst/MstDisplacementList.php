<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;

/**
 * 排気量リストマスタモデル
 */
class MstDisplacementList extends Model
{
    protected $connection = 'mst';
    protected $table      = 'mst_displacement_lists';

    protected $fillable = [
        'version_id',
        'name',
        'min_amount',
        'max_amount',
        'is_unlimited',
    ];

    protected $casts = [
        'is_unlimited' => 'boolean',
        'min_amount'   => 'integer',
        'max_amount'   => 'integer',
    ];
}
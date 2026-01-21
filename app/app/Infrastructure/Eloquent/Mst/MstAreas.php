<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstAreas extends Model
{
    protected $connection = 'mst';
    protected $table = 'mst_areas';

    protected $fillable = [
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function regions(): HasMany
    {
        return $this->hasMany(MstRegions::class, 'area_code');
    }
}
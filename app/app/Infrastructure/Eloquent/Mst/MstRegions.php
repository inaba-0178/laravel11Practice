<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstRegions extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_regions';

    protected $fillable = [
        'area_code',
        'name',
        'url',
        'query_param',
        'sort_order'
    ];

    protected $casts = [
        'area_code' => 'integer',
        'sort_order' => 'integer',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(MstAreas::class, 'area_code');
    }
}

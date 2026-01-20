<?php

namespace App\Models\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstRegions extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_regions';

    protected $fillable = [
        'name', 'url', 'query_param', 'sort_order'
    ];

    // public function Regions()
    // {
    //     return $this->hasOne(MstRegion::class, 'id');
    // }
}

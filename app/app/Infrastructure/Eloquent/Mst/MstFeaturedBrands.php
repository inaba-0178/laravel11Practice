<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstFeaturedBrands extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_featured_brands';

    protected $fillable = [
        'manufacturer_code',
        'position',
        'sort_order',
        'is_active',
    ];
}

<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstFeaturedBodyTypes extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_featured_body_types';

    protected $fillable = [
        'body_type_code',
        'position',
        'sort_order',
        'is_active',
    ];
}

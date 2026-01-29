<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstBodyTypeImages extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_body_type_images';

    protected $fillable = [
        'body_type_id',
        'image_type',
        'file_path',
        'alt_text',
        'sort_order',
        'is_main',
        'is_active',
    ];


}

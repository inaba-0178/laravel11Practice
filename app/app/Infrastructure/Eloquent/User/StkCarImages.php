<?php

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StkCarImages extends Model
{
    use HasFactory;

    protected $connection = 'user'; 
    protected $table = 'stk_car_images';

    protected $fillable = [
            'id',
            'car_id',
            'image_url', 
            'image_type',
            'display_order',
            'is_main',
            'created_at',
            'updated_at'
        ];

}

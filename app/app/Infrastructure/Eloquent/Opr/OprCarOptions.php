<?php

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OprCarOptions extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'opr_car_options';

    protected $fillable = [
            'id',
            'car_id',
            'option_category', 
            'option_name',
            'is_equipped',
            'display_order',
            'created_at',
            'updated_at'
        ];

}

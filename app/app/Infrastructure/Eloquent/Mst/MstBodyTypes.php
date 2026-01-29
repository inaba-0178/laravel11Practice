<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstBodyTypes extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_body_types';

    protected $fillable = [
        'name',
        'name_kana',
        'code',
        'url',
        'description',
        'available_countries',
        'sort_order',
        'is_active',
    ];


}

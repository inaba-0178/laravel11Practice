<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstManufacturers extends Model
{
    use HasFactory;

    protected $connection = 'mst'; 
    protected $table = 'mst_manufacturers';

    protected $fillable = [
        'name',
        'name_kana',
        'display_name',
        'code',
        'url',
        'description',
        'country_code',
        'sort_order',
        'is_active',
    ];


}

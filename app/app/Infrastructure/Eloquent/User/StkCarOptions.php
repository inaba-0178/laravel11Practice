<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StkCarOptions extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table = 'stk_car_options';

    protected $fillable = [
        'car_id',
        'option_category',
        'option_name',
        'is_equipped',
        'display_order',
    ];

    protected $casts = [
        'is_equipped' => 'boolean',
    ];

    public function car()
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }
}
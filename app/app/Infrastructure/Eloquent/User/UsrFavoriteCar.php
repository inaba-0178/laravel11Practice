<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsrFavoriteCar extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'usr_favorite_cars';

    protected $fillable = [
        'user_id',
        'car_id',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }
}
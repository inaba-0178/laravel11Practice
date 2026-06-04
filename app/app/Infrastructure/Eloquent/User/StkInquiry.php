<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkInquiry extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_inquiries';

    protected $fillable = [
        'dealer_id',
        'car_id',
        'member_id',
        'status',
        'inquiry_type',
        'name',
        'phone',
        'email',
        'postal_code',
        'address',
        'message',
        'reply',
        'replied_at',
        'replied_by',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function car()
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }
}
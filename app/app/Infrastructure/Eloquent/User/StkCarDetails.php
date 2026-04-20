<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Eloquent\User\StkDealerReview;

class StkCarDetails extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table = 'stk_car_details';

    protected $fillable = [
        'car_id',
        'first_registration_date',
        'inspection_expire_date',
        'inspection_status',
        'drive_system',
        'displacement',
        'steering_wheel',
        'number_of_doors',
        'slide_door',
        'riding_capacity',
        'loan_available',
        'description',
        'free_text',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'first_registration_date' => 'date',
        'inspection_expire_date' => 'date',
        'loan_available' => 'boolean',
    ];

    public function car()
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(StkDealerReview::class, 'dealer_id');
    }
}
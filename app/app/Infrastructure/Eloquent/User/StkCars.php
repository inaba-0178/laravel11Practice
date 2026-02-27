<?php

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OprCars extends Model
{
    use HasFactory;

    protected $connection = 'user'; 
    protected $table = 'stk_cars';

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

}

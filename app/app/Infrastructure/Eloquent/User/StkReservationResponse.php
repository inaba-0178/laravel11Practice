<?php
namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;

class StkReservationResponse extends Model
{
    protected $connection = 'user';
    protected $table      = 'stk_reservation_responses';

    protected $fillable = [
        'reservation_id',
        'handled_at',
        'handled_by',
        'customer_name',
        'customer_phone',
        'customer_address',
        'visit_purpose',
        'response_content',
        'has_estimate',
        'estimate_amount',
        'discount_amount',
        'miscellaneous_cost',
        'has_purchase',
        'purchase_car_id',
        'payment_method',
        'loan_down_payment',
        'loan_monthly_amount',
        'loan_count',
        'contract_date',
        'delivery_date',
    ];

    protected $casts = [
        'handled_at'    => 'datetime',
        'has_estimate'  => 'boolean',
        'has_purchase'  => 'boolean',
        'contract_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function reservation()
    {
        return $this->belongsTo(StkReservation::class, 'reservation_id');
    }

    public function handledBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'handled_by');
    }
}
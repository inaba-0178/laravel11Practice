<?php

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;

class OprLoanDownOption extends Model
{
    protected $connection = 'mst';
    protected $table = 'opr_loan_down_options';

    protected $fillable = [
        'value',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order'    => 'integer',
        'is_active'     => 'boolean',
    ];
}
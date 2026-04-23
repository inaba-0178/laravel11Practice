<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkAffiliatedStore extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_affiliated_stores';

    protected $fillable = [
        'dealer_id',
        'affiliated_dealer_id',
        'type',
        'status',
        'requested_by',
        'requested_at',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'dissolved_by',
        'dissolved_at',
        'dissolved_reason',
        'sort_order',
    ];

    protected $casts = [
        'dealer_id'           => 'integer',
        'affiliated_dealer_id'=> 'integer',
        'requested_by'        => 'integer',
        'approved_by'         => 'integer',
        'dissolved_by'        => 'integer',
        'sort_order'          => 'integer',
        'requested_at'        => 'datetime',
        'approved_at'         => 'datetime',
        'dissolved_at'        => 'datetime',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function affiliatedDealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'affiliated_dealer_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function dissolvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'dissolved_by');
    }
}
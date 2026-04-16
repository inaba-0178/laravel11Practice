<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkDealerContent extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_contents';

    protected $fillable = [
        'dealer_id',
        'category',
        'title',
        'description',
        'image_path',
        'sort_order',
        'is_active',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'dealer_id'  => 'integer',
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
        'started_at' => 'date',
        'ended_at'   => 'date',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }
}
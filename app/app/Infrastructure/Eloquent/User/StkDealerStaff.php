<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class StkDealerStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_staffs';

    protected $fillable = [
        'dealer_id',
        'user_id',
        'name',
        'position',
        'image_path',
        'comment',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'dealer_id'  => 'integer',
        'user_id'    => 'integer',
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StkDealerImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table = 'stk_dealer_images';

    protected $fillable = [
        'dealer_id',
        'image_path',
        'alt_text',
        'is_main',
        'sort_order',
    ];

    protected $casts = [
        'is_main'    => 'boolean',
        'sort_order' => 'integer',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function text()
    {
        return $this->hasOne(StkDealerImageText::class, 'image_id');
    }
}
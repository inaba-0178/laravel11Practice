<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StkDealerImageText extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table = 'stk_dealer_image_texts';

    protected $fillable = [
        'image_id',
        'caption',
    ];

    public function image()
    {
        return $this->belongsTo(StkDealerImage::class, 'image_id');
    }
}
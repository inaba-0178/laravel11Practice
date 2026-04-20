<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class StkDealerReviewReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_review_replies';

    protected $fillable = [
        'review_id',
        'dealer_id',
        'user_id',
        'responder_name',
        'body',
        'deleted_reason',
    ];

    protected $casts = [
        'review_id' => 'integer',
        'dealer_id' => 'integer',
        'user_id'   => 'integer',
    ];

    public function review()
    {
        return $this->belongsTo(StkDealerReview::class, 'review_id');
    }

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
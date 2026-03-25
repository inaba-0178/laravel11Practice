<?php
namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class StkReservation extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_reservations';

    protected $fillable = [
        'dealer_id',
        'car_id',
        'member_id',
        'reservation_type_id',
        'schedule_id',
        'status',
        'memo',
        'visit_reason',
        'handled_by',
        'handled_at',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_address',
    ];

    protected $casts = [
        'dealer_id'           => 'integer',
        'car_id'              => 'integer',
        'reservation_type_id' => 'integer',
        'schedule_id'         => 'integer',
        'handled_at'          => 'datetime',
    ];

    // ディーラー
    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    // 車両
    public function car()
    {
        return $this->belongsTo(StkCar::class, 'car_id');
    }

    // スケジュール（予約日時はここから取得）
    public function schedule()
    {
        return $this->belongsTo(StkDealerSchedule::class, 'schedule_id');
    }

    // 会員（ゲストの場合はnull）
    public function member()
    {
        return $this->belongsTo(UsrUser::class, 'member_id', 'id');
    }

    // 対応者（管理者）
    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function response()
    {
        return $this->hasOne(StkReservationResponse::class, 'reservation_id');
    }
}
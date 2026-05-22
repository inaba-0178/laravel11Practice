<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Eloquent\Opr\OprReservationTypes;

class StkDealerSchedule extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'stk_dealer_schedules';

    protected $fillable = [
        'dealer_id',
        'reservation_type_id',
        'date',
        'time_from',
        'time_to',
        'max_reservations',
        'is_available',
        'is_closed',
        'delete_reason',
    ];

    protected $casts = [
        'date'             => 'date',
        'is_available'     => 'boolean',
        'is_closed'        => 'boolean',
        'max_reservations' => 'integer',
        'deleted_at'       => 'datetime',
    ];

    // -------------------------------------------------------
    // リレーション
    // -------------------------------------------------------

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function reservationType(): BelongsTo
    {
        return $this->belongsTo(OprReservationTypes::class, 'reservation_type_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StkReservation::class, 'schedule_id');
    }

    // -------------------------------------------------------
    // スコープ
    // -------------------------------------------------------

    /**
     * 予約受付中のスケジュールのみ
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * 指定ディーラーのスケジュール
     */
    public function scopeForDealer($query, int $dealerId)
    {
        return $query->where('dealer_id', $dealerId);
    }

    /**
     * 指定月のスケジュール（YYYY-MM形式）
     */
    public function scopeInMonth($query, string $month)
    {
        return $query->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month]);
    }

    /**
     * 指定期間のスケジュール
     */
    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    // -------------------------------------------------------
    // ヘルパー
    // -------------------------------------------------------

    /**
     * アクティブな予約数（pending/confirmed）を取得
     */
    public function getActiveReservationCountAttribute(): int
    {
        return $this->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * 満枠かどうか
     */
    public function getIsFullAttribute(): bool
    {
        return $this->active_reservation_count >= $this->max_reservations;
    }
}
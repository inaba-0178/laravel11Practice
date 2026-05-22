<?php

namespace App\Domain\DealerSchedule\Services;

use App\Infrastructure\Eloquent\User\StkDealerSchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DealerScheduleService
{
    // -------------------------------------------------------
    // 一括生成（曜日別・種別別スロット指定版）
    // -------------------------------------------------------

    /**
     * 曜日別・種別別スロット指定でスケジュールを一括生成する
     * 定休日曜日・特定日は is_closed=1 のレコードを生成する
     *
     * @param int    $dealerId
     * @param string $dateFrom
     * @param string $dateTo
     * @param array  $weekdaySlots
     *   [
     *     0 => [ // 0=月 〜 6=日
     *       'is_holiday'  => bool,
     *       'type_slots'  => [
     *         ['type_id'=>1, 'time_from'=>'10:00', 'time_to'=>'12:00', 'max_reservations'=>2],
     *         ...
     *       ]
     *     ],
     *     ...
     *   ]
     * @param array  $specificHolidays 特定日除外 ['2026-05-24', ...]
     * @return array ['created'=>int, 'skipped'=>int]
     */
    public function bulkGenerateByTypeSlots(
        int    $dealerId,
        string $dateFrom,
        string $dateTo,
        array  $weekdaySlots,
        array  $specificHolidays = [],
    ): array {
        $created  = 0;
        $skipped  = 0;
        $period   = CarbonPeriod::create($dateFrom, $dateTo);
        $existing = $this->getExistingKeys($dealerId, $dateFrom, $dateTo);

        DB::connection('user')->transaction(function () use (
            $period, $dealerId, $weekdaySlots,
            $specificHolidays, $existing, &$created, &$skipped
        ) {
            $inserts = [];

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');

                // 特定日除外 → is_closedレコード生成
                if (in_array($dateStr, $specificHolidays, true)) {
                    $closedKey = "{$dateStr}_closed";
                    if (!isset($existing[$closedKey])) {
                        $inserts[]            = $this->buildClosedRecord($dealerId, $dateStr);
                        $existing[$closedKey] = true;
                        $created++;
                    }
                    continue;
                }

                $weekday    = $this->carbonDayToIndex($date);
                $daySetting = $weekdaySlots[$weekday] ?? null;
                if (!$daySetting) continue;

                // 定休日曜日 → is_closedレコード生成
                if ($daySetting['is_holiday'] ?? false) {
                    $closedKey = "{$dateStr}_closed";
                    if (!isset($existing[$closedKey])) {
                        $inserts[]            = $this->buildClosedRecord($dealerId, $dateStr);
                        $existing[$closedKey] = true;
                        $created++;
                    }
                    continue;
                }

                $typeSlots = $daySetting['type_slots'] ?? [];
                if (empty($typeSlots)) continue;

                foreach ($typeSlots as $slot) {
                    $typeId   = $slot['type_id'];
                    $timeFrom = $slot['time_from'];
                    $timeTo   = $slot['time_to'];
                    $max      = (int)($slot['max_reservations'] ?? 1);
                    $key      = "{$dateStr}_{$typeId}_{$timeFrom}_{$timeTo}";

                    if (isset($existing[$key])) {
                        $skipped++;
                        continue;
                    }

                    $now       = now();
                    $inserts[] = [
                        'dealer_id'           => $dealerId,
                        'reservation_type_id' => $typeId,
                        'date'                => $dateStr,
                        'time_from'           => $timeFrom,
                        'time_to'             => $timeTo,
                        'max_reservations'    => $max,
                        'is_available'        => 1,
                        'is_closed'           => 0,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];

                    $existing[$key] = true;
                    $created++;
                }
            }

            // 500件単位でinsert（メモリ対策）
            foreach (array_chunk($inserts, 500) as $chunk) {
                StkDealerSchedule::insert($chunk);
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }

    // -------------------------------------------------------
    // 論理削除
    // -------------------------------------------------------

    /**
     * 指定IDのスケジュールを論理削除する
     *
     * @param array  $scheduleIds
     * @param string $deleteReason
     * @return int 削除件数
     */
    public function softDeleteSchedules(array $scheduleIds, string $deleteReason): int
    {
        return StkDealerSchedule::whereIn('id', $scheduleIds)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at'    => now(),
                'delete_reason' => $deleteReason,
            ]);
    }

    // -------------------------------------------------------
    // 時間帯追加
    // -------------------------------------------------------

    /**
     * 指定日に時間帯を追加する
     * 重複時は null を返す
     *
     * @param int    $dealerId
     * @param string $date
     * @param int    $reservationTypeId
     * @param string $timeFrom
     * @param string $timeTo
     * @param int    $maxReservations
     * @return StkDealerSchedule|null
     */
    public function addSlot(
        int    $dealerId,
        string $date,
        int    $reservationTypeId,
        string $timeFrom,
        string $timeTo,
        int    $maxReservations = 1,
    ): ?StkDealerSchedule {
        $exists = StkDealerSchedule::withTrashed()
            ->where('dealer_id',           $dealerId)
            ->where('reservation_type_id', $reservationTypeId)
            ->where('date',                $date)
            ->where('time_from',           $timeFrom)
            ->where('time_to',             $timeTo)
            ->exists();

        if ($exists) return null;

        return StkDealerSchedule::create([
            'dealer_id'           => $dealerId,
            'reservation_type_id' => $reservationTypeId,
            'date'                => $date,
            'time_from'           => $timeFrom,
            'time_to'             => $timeTo,
            'max_reservations'    => $maxReservations,
            'is_available'        => 1,
            'is_closed'           => 0,
        ]);
    }

    // -------------------------------------------------------
    // 定休日レコード生成
    // -------------------------------------------------------

    /**
     * 指定日に定休日レコードを1件生成する
     * 重複時は null を返す
     *
     * @param int    $dealerId
     * @param string $date
     * @return StkDealerSchedule|null
     */
    public function addClosedDay(int $dealerId, string $date): ?StkDealerSchedule
    {
        $exists = StkDealerSchedule::withTrashed()
            ->where('dealer_id', $dealerId)
            ->where('date',      $date)
            ->where('is_closed', 1)
            ->exists();

        if ($exists) return null;

        return StkDealerSchedule::create([
            'dealer_id'           => $dealerId,
            'reservation_type_id' => 0,
            'date'                => $date,
            'time_from'           => '00:00:00',
            'time_to'             => '00:00:00',
            'max_reservations'    => 0,
            'is_available'        => 0,
            'is_closed'           => 1,
        ]);
    }

    // -------------------------------------------------------
    // カレンダー表示用データ取得
    // -------------------------------------------------------

    /**
     * 指定期間のスケジュールをカレンダー表示用に日付キーでグループ化して返す
     * 定休日レコード（is_closed=1）も含む
     *
     * @param int    $dealerId
     * @param string $dateFrom YYYY-MM-DD
     * @param string $dateTo   YYYY-MM-DD
     * @return Collection ['2026-05-01' => Collection<StkDealerSchedule>, ...]
     */
    public function getSchedulesGroupedByDate(int $dealerId, string $dateFrom, string $dateTo): Collection
    {
        return StkDealerSchedule::with('reservationType')
            ->forDealer($dealerId)
            ->betweenDates($dateFrom, $dateTo)
            ->orderBy('date')
            ->orderBy('is_closed', 'desc') // 定休日レコードを先頭に
            ->orderBy('reservation_type_id')
            ->orderBy('time_from')
            ->get()
            ->groupBy(fn($s) => $s->date->format('Y-m-d'));
    }

    /**
     * 指定日のスケジュール一覧（モーダル用）
     *
     * @param int    $dealerId
     * @param string $date YYYY-MM-DD
     * @return Collection<StkDealerSchedule>
     */
    public function getSchedulesByDate(int $dealerId, string $date): Collection
    {
        return StkDealerSchedule::with(['reservationType', 'reservations'])
            ->forDealer($dealerId)
            ->where('date', $date)
            ->orderBy('reservation_type_id')
            ->orderBy('time_from')
            ->get();
    }

    // -------------------------------------------------------
    // プレビュー用（Page側から呼び出し）
    // -------------------------------------------------------

    /**
     * 既存レコードキーをpublicで公開（プレビュー生成用）
     */
    public function getExistingKeysPublic(int $dealerId, string $dateFrom, string $dateTo): array
    {
        return $this->getExistingKeys($dealerId, $dateFrom, $dateTo);
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    /**
     * 既存レコードを "{date}_{typeId}_{timeFrom}_{timeTo}" 形式のキーで取得
     * 定休日は "{date}_closed" キーで管理
     */
    private function getExistingKeys(int $dealerId, string $dateFrom, string $dateTo): array
    {
        return StkDealerSchedule::withTrashed()
            ->forDealer($dealerId)
            ->betweenDates($dateFrom, $dateTo)
            ->get(['date', 'reservation_type_id', 'time_from', 'time_to', 'is_closed'])
            ->mapWithKeys(function ($s) {
                $date = $s->date->format('Y-m-d');
                if ($s->is_closed) {
                    return ["{$date}_closed" => true];
                }
                $timeFrom = substr($s->time_from, 0, 5);
                $timeTo   = substr($s->time_to,   0, 5);
                return ["{$date}_{$s->reservation_type_id}_{$timeFrom}_{$timeTo}" => true];
            })
            ->toArray();
    }

    /**
     * is_closed=1 の insert 配列を生成
     */
    private function buildClosedRecord(int $dealerId, string $dateStr): array
    {
        $now = now();
        return [
            'dealer_id'           => $dealerId,
            'reservation_type_id' => 0,
            'date'                => $dateStr,
            'time_from'           => '00:00:00',
            'time_to'             => '00:00:00',
            'max_reservations'    => 0,
            'is_available'        => 0,
            'is_closed'           => 1,
            'created_at'          => $now,
            'updated_at'          => $now,
        ];
    }

    /**
     * Carbonの曜日（0=日曜〜6=土曜）を
     * 設定配列のインデックス（0=月〜6=日）に変換
     */
    private function carbonDayToIndex(Carbon $date): int
    {
        $day = $date->dayOfWeek;
        return $day === 0 ? 6 : $day - 1;
    }
}
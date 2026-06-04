<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Analytics;

use App\Application\UseCases\Analytics\RecordViewCountInputData;
use App\Infrastructure\Eloquent\Opr\OprSetting;
use App\Infrastructure\Eloquent\User\StkAnalyticsView;
use Carbon\Carbon;

/**
 * 閲覧数リポジトリ（Eloquent実装）
 *
 * 閲覧数の記録・重複チェックを担当する。
 */
class EloquentAnalyticsRepository
{
    /**
     * 重複チェックして閲覧数を記録する
     *
     * 同一ユーザー・同一車両でview_count_interval_minutes以内の場合は記録しない。
     *
     * @param RecordViewCountInputData $data
     * @return bool 記録した場合はtrue・重複の場合はfalse
     */
    public function recordIfNotDuplicate(RecordViewCountInputData $data): bool
    {
        // 重複チェック間隔を設定から取得（デフォルト60分）
        $intervalMinutes = (int) OprSetting::getValue('view_count_interval_minutes', 60);
        $since           = Carbon::now()->subMinutes($intervalMinutes);

        // 重複チェック
        $query = StkAnalyticsView::where('car_id', $data->carId->getValue())
            ->where('viewed_at', '>=', $since);

        if ($data->memberId) {
            // ログイン済み：member_idで重複チェック
            $query->where('member_id', $data->memberId);
        } else {
            // 未ログイン：cookie_idで重複チェック
            $query->where('cookie_id', $data->cookieId);
        }

        if ($query->exists()) {
            return false; // 重複のため記録しない
        }

        // 閲覧数を記録
        StkAnalyticsView::create([
            'dealer_id' => $data->dealerId->getValue(),
            'car_id'    => $data->carId->getValue(),
            'member_id' => $data->memberId,
            'cookie_id' => $data->cookieId,
            'viewed_at' => Carbon::now(),
        ]);

        return true;
    }
}
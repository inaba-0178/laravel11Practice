<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Application\Services\AuditLogger;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;

class RecordLoginLog
{
    public function handle(Login $event): void
    {
        if (!($event->user instanceof User)) {
            return;
        }

        // APIログインはAuthControllerで直接記録しているためスキップ
        if (request()->is('api/*')) {
            return;
        }

        $user     = $event->user;
        $cacheKey = 'login_logged_admin_' . $user->id;

        // 2FA等で短時間に複数回イベントが発火するケースを除外
        if (Cache::has($cacheKey)) {
            return;
        }
        Cache::put($cacheKey, true, 30);

        AuditLogger::logAuth(
            action:       'login',
            userId:       $user->id,
            userName:     $user->name,
            operatorType: 'admin',
            result:       'success',
        );
    }
}

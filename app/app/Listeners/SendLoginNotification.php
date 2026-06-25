<?php

namespace App\Listeners;

use App\Infrastructure\Notifications\Mail\LoginNotificationMail;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Mail;

class SendLoginNotification
{
    public function handle(Login $event): void
    {
        // 管理ユーザー（User モデル）のログインのみ対象
        if (!($event->user instanceof User)) {
            return;
        }

        $user = $event->user;
        if (empty($user->email)) {
            return;
        }

        try {
            Mail::to($user->email)->send(new LoginNotificationMail(
                userName:  $user->name,
                loginAt:   now()->format('Y年m月d日 H:i:s'),
                ipAddress: request()->ip() ?? '不明',
                userAgent: substr(request()->userAgent() ?? '不明', 0, 200),
            ));
        } catch (\Throwable) {
            // メール送信失敗はアプリを止めない
        }
    }
}

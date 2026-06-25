<?php

namespace App\Console\Commands;

use App\Infrastructure\Notifications\Mail\TwoFactorReminderMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTwoFactorReminderCommand extends Command
{
    protected $signature = 'security:send-2fa-reminder';
    protected $description = '2FA未設定の管理ユーザーへリマインドメールを送信する';

    public function handle(): int
    {
        $adminUrl = config('app.url') . '/admin';

        $users = User::where('is_active', true)
            ->whereDoesntHave('breezySessions', function ($query) {
                $query->whereNotNull('two_factor_confirmed_at');
            })
            ->get();

        if ($users->isEmpty()) {
            $this->info('2FA未設定のユーザーはいません。');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($users as $user) {
            if (empty($user->email)) {
                continue;
            }
            try {
                Mail::to($user->email)->send(new TwoFactorReminderMail(
                    userName: $user->name,
                    adminUrl: $adminUrl,
                ));
                $sent++;
            } catch (\Throwable $e) {
                $this->warn("送信失敗: {$user->email} - {$e->getMessage()}");
            }
        }

        $this->info("リマインドメールを {$sent} 件送信しました。");
        return self::SUCCESS;
    }
}

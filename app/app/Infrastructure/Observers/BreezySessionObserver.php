<?php

namespace App\Infrastructure\Observers;

use App\Infrastructure\Notifications\Mail\TwoFactorSetupMail;
use Illuminate\Support\Facades\Mail;
use Jeffgreco13\FilamentBreezy\Models\BreezySession;

class BreezySessionObserver
{
    public function updated(BreezySession $breezySession): void
    {
        // two_factor_confirmed_at が null → 値にセットされた瞬間 = 2FA初回設定完了
        if (
            $breezySession->wasChanged('two_factor_confirmed_at')
            && $breezySession->two_factor_confirmed_at !== null
            && $breezySession->getOriginal('two_factor_confirmed_at') === null
        ) {
            $user = $breezySession->authenticatable;
            if ($user === null || empty($user->email)) {
                return;
            }

            try {
                Mail::to($user->email)->send(new TwoFactorSetupMail(
                    userName:    $user->name,
                    confirmedAt: $breezySession->two_factor_confirmed_at->format('Y年m月d日 H:i:s'),
                    ipAddress:   request()->ip() ?? '不明',
                ));
            } catch (\Throwable) {
                // メール送信失敗はアプリを止めない
            }
        }
    }
}

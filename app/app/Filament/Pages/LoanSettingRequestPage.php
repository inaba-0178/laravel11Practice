<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Infrastructure\Eloquent\User\StkCarDealer;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LoanSettingRequestPage extends Page
{
    protected static string  $view           = 'filament.pages.loan-setting-request';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'ディーラーメニュー';
    protected static ?string $title          = 'ローン設定申請';

    public ?StkCarDealer $dealer = null;

    public string $reason = '';

    public function mount(): void
    {
        $this->dealer = StkCarDealer::find(auth()->user()->dealer_id);
    }

    public function getTitle(): string
    {
        return 'ローン設定申請';
    }

    // ===== 申請を送信 =====
    public function submitRequest(): void
    {
        \Log::info('submitRequest called', [
            'dealer'  => $this->dealer?->id,
            'reason'  => $this->reason,
            'user_id' => auth()->id(),
        ]);

        $this->validate([
            'reason' => 'required|min:10',
        ], [
            'reason.required' => '申請理由を入力してください',
            'reason.min'      => '申請理由は10文字以上入力してください',
        ]);
 \Log::info('validation passed');
       $result =  $this->dealer->update([
            'loan_setting_requested_by' => auth()->id(),
            'loan_setting_reason'       => $this->reason,
            'loan_setting_requested_at' => now(),
        ]);
\Log::info('update result', ['result' => $result, 'dealer' => $this->dealer->toArray()]);
        $this->reason = '';
        $this->dealer->refresh();

        Notification::make()
            ->title('ローン設定の申請を送信しました。管理者の承認をお待ちください。')
            ->success()
            ->send();
    }

    // ===== 申請を取り消す =====
    public function cancelRequest(): void
    {
        $this->dealer->update([
            'loan_setting_requested_by'    => auth()->id(),
            'loan_setting_reason'          => $this->reason,
            'loan_setting_requested_at'    => now(),
            'loan_setting_rejected_reason' => null,  // 追加：拒否理由をクリア
            'loan_setting_approved_at'     => null,  // 追加：承認日時もクリア
            'loan_setting_approved_by'     => null,  // 追加：承認者もクリア
        ]);

        $this->dealer->refresh();

        Notification::make()
            ->title('申請を取り消しました')
            ->warning()
            ->send();
    }
}
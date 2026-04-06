<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanSettingResource\Pages;

use App\Filament\Resources\LoanSettingResource;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ViewLoanSetting extends Page
{
    protected static string $resource = LoanSettingResource::class;
    protected static string $view     = 'filament.pages.loan-setting-view';

    public StkCarDealer $record;

    public function mount(StkCarDealer $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'ローン設定申請詳細';
    }

    public function getBreadcrumb(): string
    {
        return 'ローン設定申請詳細';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('← 一覧に戻る')
                ->color('gray')
                ->url(ListLoanSettings::getUrl()),
        ];
    }

    // ===== 許可 =====
    public function approve(): void
    {
        $this->record->update([
            'loan_setting_enabled'         => 1,
            'loan_setting_approved_by'     => auth()->id(),
            'loan_setting_approved_at'     => now(),
            'loan_setting_rejected_reason' => null,
        ]);

        Notification::make()->title('ローン設定を許可しました')->success()->send();
        $this->redirect(ListLoanSettings::getUrl());
    }

    // ===== 拒否 =====
    public function reject(string $reason): void
    {
        $this->record->update([
            'loan_setting_enabled'         => 0,
            'loan_setting_rejected_reason' => $reason,
            'loan_setting_approved_by'     => auth()->id(),
            'loan_setting_approved_at'     => now(),
        ]);

        Notification::make()->title('ローン設定申請を拒否しました')->danger()->send();
        $this->redirect(ListLoanSettings::getUrl());
    }

    // ===== 拒否解除（申請中の状態に戻す） =====
    public function resetRejection(): void
    {
        $this->record->update([
            'loan_setting_rejected_reason' => null,
            'loan_setting_approved_by'     => null,
            'loan_setting_approved_at'     => null,
            // loan_setting_requested_at・requested_by・reason はそのまま残す
        ]);

        Notification::make()
            ->title('拒否を解除しました。申請中の状態に戻りました。')
            ->success()
            ->send();

        $this->redirect(ListLoanSettings::getUrl());
    }

    // ===== 許可を取り消す =====
    public function revoke(): void
    {
        $this->record->update([
            'loan_setting_enabled'     => 0,
            'loan_setting_approved_by' => null,
            'loan_setting_approved_at' => null,
        ]);

        Notification::make()->title('ローン設定の許可を取り消しました')->warning()->send();
        $this->redirect(ListLoanSettings::getUrl());
    }
}
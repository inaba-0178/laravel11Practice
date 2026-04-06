<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerLoanResource\Pages;

use App\Constants\LoanMonths;
use App\Filament\Resources\DealerLoanResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditDealerLoan extends EditRecord
{
    protected static string $resource = DealerLoanResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        \Log::info('mutateFormDataBeforeSave', [
        'loans' => $data['loans'] ?? 'なし',
    ]);
        $data['months_options'] = $this->generateMonthsOptions(
            (int) $data['min_months'],
            (int) $data['max_months']
        );
        return $data;
    }

    private function generateMonthsOptions(int $min, int $max): array
    {
        return collect(LoanMonths::OPTIONS)
            ->keys()
            ->filter(fn ($month) => $month >= $min && $month <= $max)
            ->values()
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        $record  = $this->getRecord();
        $actions = [];

        if (is_null($record->deleted_at) && is_null($record->delete_requested_at)) {

            // 保存ボタン
            $actions[] = Action::make('save')
                ->label('保存')
                ->action(function () {
                    $this->save();
                });

            // 有効・無効切り替え
            if ($record->is_active) {
                $actions[] = Action::make('deactivate')
                    ->label('無効化する')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('このプランを無効化しますか？')
                    ->modalDescription('無効化するとこのプランは新規車両に選択できなくなります。既存の車両はスナップショットのデータが維持されます。無効化後はシステムデフォルトプランにフォールバックします。')
                    ->modalSubmitActionLabel('無効化する')
                    ->action(function () use ($record) {
                        $record->update(['is_active' => 0]);
                        Notification::make()->title('プランを無効化しました')->warning()->send();
                        $this->redirect($this->getResource()::getUrl('index'));
                    });
            } else {
                $actions[] = Action::make('activate')
                    ->label('有効化する')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('このプランを有効化しますか？')
                    ->modalSubmitActionLabel('有効化する')
                    ->action(function () use ($record) {
                        $record->update(['is_active' => 1]);
                        Notification::make()->title('プランを有効化しました')->success()->send();
                        $this->redirect($this->getResource()::getUrl('index'));
                    });

                // 削除申請（無効化済みのみ）
                $actions[] = Action::make('request_delete')
                    ->label('削除申請する')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('reason')
                            ->label('削除理由')
                            ->required()
                            ->rows(3)
                            ->placeholder('削除する理由を入力してください'),
                    ])
                    ->modalHeading('削除申請')
                    ->modalDescription('管理者が承認後に削除されます。削除後はデータがログに移されます。')
                    ->modalSubmitActionLabel('申請する')
                    ->action(function (array $data) use ($record) {
                        $record->update([
                            'delete_requested_by'   => Auth::id(),
                            'delete_request_reason' => $data['reason'],
                            'delete_requested_at'   => now(),
                        ]);
                        Notification::make()->title('削除申請を送信しました')->success()->send();
                        $this->redirect($this->getResource()::getUrl('index'));
                    });
            }
        }

        // 一覧に戻る
        $actions[] = Action::make('back')
            ->label('一覧に戻る')
            ->color('gray')
            ->url($this->getResource()::getUrl('index'));

        return $actions;
    }
}
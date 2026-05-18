<?php

declare(strict_types=1);

namespace App\Filament\Resources\MstVersionResource\Pages;

use App\Constants\Role\RoleManagement;
use App\Filament\Resources\MstVersionResource;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMstVersion extends ViewRecord
{
    protected static string $resource = MstVersionResource::class;

    protected function getHeaderActions(): array
    {
        $record  = $this->getRecord();
        $user    = Auth::user();
        $actions = [];

        // 承認申請ボタン（draft → pending）
        if ($record->isDraft() && in_array($user->role, RoleManagement::MST_OPERATOR_ROLES)) {
            $actions[] = Action::make('request_approval')
                ->label('承認申請')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('承認申請しますか？')
                ->modalSubmitActionLabel('申請する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record, $user) {
                    $record->update([
                        'status'       => 'pending',
                        'requested_by' => $user->id,
                        'requested_at' => now(),
                    ]);

                    Notification::make()
                        ->title('承認申請しました')
                        ->success()
                        ->send();

                    $this->redirect(MstVersionResource::getUrl('view', ['record' => $record->id]));
                });
        }

        // 承認ボタン（pending → approved）
        if ($record->isPending() && in_array($user->role, RoleManagement::MST_APPROVER_ROLES)) {
            // 自分が申請したものは承認できない
            if ($record->requested_by !== $user->id) {
                $actions[] = Action::make('approve')
                    ->label('承認')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('承認しますか？')
                    ->modalSubmitActionLabel('承認する')
                    ->modalCancelActionLabel('キャンセル')
                    ->action(function () use ($record, $user) {
                        $record->update([
                            'status'      => 'approved',
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('承認しました')
                            ->success()
                            ->send();

                        $this->redirect(MstVersionResource::getUrl('view', ['record' => $record->id]));
                    });

                // 却下ボタン
                $actions[] = Action::make('reject')
                    ->label('却下')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejected_reason')
                            ->label('却下理由')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('却下しますか？')
                    ->modalSubmitActionLabel('却下する')
                    ->modalCancelActionLabel('キャンセル')
                    ->action(function (array $data) use ($record, $user) {
                        $record->update([
                            'status'          => 'draft',
                            'rejected_reason' => $data['rejected_reason'],
                        ]);

                        Notification::make()
                            ->title('却下しました')
                            ->danger()
                            ->send();

                        $this->redirect(MstVersionResource::getUrl('view', ['record' => $record->id]));
                    });
            }
        }

        // 有効化ボタン（approved → active）
        if ($record->isApproved() && in_array($user->role, RoleManagement::MST_APPROVER_ROLES)) {
            $actions[] = Action::make('activate')
                ->label('有効化')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('有効化しますか？')
                ->modalDescription('現在のアクティブバージョンはアーカイブされます。')
                ->modalSubmitActionLabel('有効化する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record, $user) {
                    // 現在のactiveをarchivedに
                    MstVersion::where('status', 'active')->update(['status' => 'archived']);

                    $record->update([
                        'status'       => 'active',
                        'activated_by' => $user->id,
                        'activated_at' => now(),
                    ]);

                    Notification::make()
                        ->title('有効化しました')
                        ->success()
                        ->send();

                    $this->redirect(MstVersionResource::getUrl('view', ['record' => $record->id]));
                });
        }

        return $actions;
    }
}
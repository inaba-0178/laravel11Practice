<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Pages\UserDetail;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected static string $view     = 'filament.pages.user-edit';

    protected function getRedirectUrl(): string
    {
        return UserDetail::getUrl(['id' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveWithConfirm')
                ->label('保存する')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('ユーザー情報を保存しますか？')
                ->modalDescription('入力内容を保存します。')
                ->modalSubmitActionLabel('保存する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    try {
                        $data = $this->form->getState();
                        $this->record->update($data);

                        Notification::make()
                            ->title('保存しました')
                            ->success()
                            ->send();

                        $this->redirect(UserDetail::getUrl(['id' => $this->record->id]));

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('エラーが発生しました')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}
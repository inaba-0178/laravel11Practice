<?php

declare(strict_types=1);

namespace App\Filament\Resources\OprSettingResource\Pages;

use App\Filament\Resources\OprSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOprSetting extends EditRecord
{
    protected static string $resource = OprSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('一覧に戻る')
                ->color('gray')
                ->url(ListOprSettings::getUrl()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return '設定を更新しました';
    }
}
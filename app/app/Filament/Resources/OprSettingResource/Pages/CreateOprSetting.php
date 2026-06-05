<?php

declare(strict_types=1);

namespace App\Filament\Resources\OprSettingResource\Pages;

use App\Filament\Resources\OprSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOprSetting extends CreateRecord
{
    protected static string $resource = OprSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return '設定を追加しました';
    }
}
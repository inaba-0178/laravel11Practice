<?php

declare(strict_types=1);

namespace App\Filament\Resources\OprMainViewResource\Pages;

use App\Filament\Resources\OprMainViewResource;
use App\Filament\Pages\OprMainViewDetail;
use Filament\Resources\Pages\EditRecord;

class EditOprMainView extends EditRecord
{
    protected static string $resource = OprMainViewResource::class;

    protected function getRedirectUrl(): string
    {
        return OprMainViewDetail::getUrl(['id' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
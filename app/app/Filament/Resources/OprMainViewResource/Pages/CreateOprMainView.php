<?php

declare(strict_types=1);

namespace App\Filament\Resources\OprMainViewResource\Pages;

use App\Filament\Resources\OprMainViewResource;
use App\Filament\Pages\OprMainViewDetail;
use Filament\Resources\Pages\CreateRecord;

class CreateOprMainView extends CreateRecord
{
    protected static string $resource = OprMainViewResource::class;

    protected function getRedirectUrl(): string
    {
        return OprMainViewDetail::getUrl(['id' => $this->record->id]);
    }
}
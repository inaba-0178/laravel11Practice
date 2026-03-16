<?php

namespace App\Filament\Resources\MailTemplateResource\Pages;

use App\Filament\Resources\MailTemplateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMailTemplate extends ViewRecord
{
    protected static string $resource = MailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('編集'),
        ];
    }

    public function getTitle(): string
    {
        return 'メールテンプレート詳細';
    }
}
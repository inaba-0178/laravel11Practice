<?php

namespace App\Filament\Resources\MailTemplateResource\Pages;

use App\Filament\Resources\MailTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMailTemplate extends EditRecord
{
    protected static string $resource = MailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('削除')
                ->modalHeading(fn() => 'メールテンプレートID:' . $this->record->id . ' 削除')
                ->modalDescription(fn() => 'メールテンプレートID:' . $this->record->id . 'を削除します。この操作は取り消せません。')
                ->modalSubmitActionLabel('削除'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'メールテンプレート編集';
    }
}
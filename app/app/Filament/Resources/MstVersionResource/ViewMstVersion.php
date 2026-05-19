<?php

declare(strict_types=1);

namespace App\Filament\Resources\MstVersionResource\Pages;

use App\Domain\Mst\Services\MstRollbackService;
use App\Filament\Resources\MstVersionResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewMstVersion extends ViewRecord
{
    protected static string $resource = MstVersionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('基本情報')
                    ->schema([
                        TextEntry::make('version')
                            ->label('バージョン')
                            ->size('lg')
                            ->weight('bold')
                            ->color(fn ($record) => $record->status === 'active' ? 'success' : null),

                        TextEntry::make('status')
                            ->label('ステータス')
                            ->badge()
                            ->color(fn (string $state) => match($state) {
                                'active'   => 'success',
                                'archived' => 'gray',
                                default    => 'gray',
                            })
                            ->formatStateUsing(fn (string $state) => match($state) {
                                'active'   => '適用中',
                                'archived' => 'アーカイブ',
                                default    => $state,
                            }),

                        TextEntry::make('description')
                            ->label('説明')
                            ->columnSpanFull()
                            ->placeholder('説明なし'),
                    ])
                    ->columns(2),

                Section::make('アップロード情報')
                    ->schema([
                        TextEntry::make('uploadedBy.name')
                            ->label('アップロード者'),

                        TextEntry::make('uploaded_at')
                            ->label('アップロード日時')
                            ->dateTime('Y/m/d H:i'),

                        TextEntry::make('activatedBy.name')
                            ->label('有効化者'),

                        TextEntry::make('activated_at')
                            ->label('有効化日時')
                            ->dateTime('Y/m/d H:i'),
                    ])
                    ->columns(2),

                Section::make('ロールバック情報')
                    ->schema([
                        TextEntry::make('rolledBackBy.name')
                            ->label('ロールバック実行者'),

                        TextEntry::make('rolled_back_at')
                            ->label('ロールバック日時')
                            ->dateTime('Y/m/d H:i'),

                        TextEntry::make('rollback_reason')
                            ->label('ロールバック理由')
                            ->columnSpanFull()
                            ->placeholder('なし'),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->rolled_back_at !== null),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rollback')
                ->label('このバージョンにロールバック')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->visible(fn () => $this->record->status !== 'active')
                ->form([
                    Textarea::make('rollback_reason')
                        ->label('ロールバック理由')
                        ->required()
                        ->rows(3)
                        ->placeholder('ロールバックする理由を入力してください'),
                ])
                ->modalHeading(fn () => "バージョン {$this->record->version} にロールバック")
                ->modalDescription(fn () => "バージョン {$this->record->version} のデータに戻します。現在のデータは上書きされます。本当によろしいですか？")
                ->modalSubmitActionLabel('ロールバック実行')
                ->modalCancelActionLabel('キャンセル')
                ->action(function (array $data) {
                    try {
                        $service = new MstRollbackService();
                        $service->rollback($this->record, $data['rollback_reason']);

                        Notification::make()
                            ->title("バージョン {$this->record->version} にロールバックしました")
                            ->success()
                            ->send();

                        $this->refreshFormData([
                            'status',
                            'activated_at',
                            'rolled_back_at',
                            'rollback_reason',
                        ]);

                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('ロールバックできません')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();

                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('ロールバックに失敗しました')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function getTitle(): string
    {
        return "バージョン {$this->record->version} 詳細";
    }
}
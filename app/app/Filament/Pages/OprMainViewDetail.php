<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\OprMainViewResource;
use App\Infrastructure\Eloquent\Opr\OprMainView;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class OprMainViewDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon           = 'heroicon-o-photo';
    protected static string  $view                     = 'filament.pages.opr-main-view-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string      $id     = null;
    public ?OprMainView $record = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = OprMainView::whereNull('deleted_at')->findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            OprMainViewResource::getUrl() => 'メインビュー',
            OprMainViewDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return 'メインビュー : [' . $this->record->title . ']';
    }

    public function infoList(): Infolist
    {
        $isActive = $this->record->is_active;
        $statusText = $isActive ? '✅ 現在 有効 で表示されています' : '❌ 現在 無効 で非表示になっています';
        $statusColor = $isActive ? 'success' : 'danger';

        $data = [
            'status_notice' => $statusText,
            'id'            => $this->record->id,
            'title'         => $this->record->title ?? '-',
            'sub'           => $this->record->sub ?? '-',
            'label'         => $this->record->label ?? '-',
            'image_path'    => $this->record->image_path ?? '-',
            'link_url'      => $this->record->link_url ?? '-',
            'sort_order'    => $this->record->sort_order,
            'is_active'     => $this->record->is_active ? '有効' : '無効',
            'start_at'      => $this->record->start_at?->format('Y/m/d H:i') ?? '-',
            'end_at'        => $this->record->end_at?->format('Y/m/d H:i') ?? '-',
            'created_at'    => $this->record->created_at,
            'updated_at'    => $this->record->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('メインビュー情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('title')->label('タイトル'),
                        TextEntry::make('sub')->label('サブテキスト'),
                        TextEntry::make('label')->label('ラベル'),
                        TextEntry::make('image_path')->label('画像パス'),
                        TextEntry::make('link_url')->label('リンクURL'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('有効'),
                        TextEntry::make('start_at')->label('表示開始日時'),
                        TextEntry::make('end_at')->label('表示終了日時'),
                    ]),

                Section::make('日時')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('編集')
                ->color('primary')
                ->url(OprMainViewResource::getUrl('edit', ['record' => $this->record->id])),

            Action::make('delete')
                ->label('削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('メインビューを削除しますか？')
                ->modalDescription('削除すると一覧から表示されなくなります。')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    $this->record->delete();

                    Notification::make()
                        ->title('削除しました')
                        ->success()
                        ->send();

                    $this->redirect(OprMainViewResource::getUrl('index'));
                }),
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\DealerContentResource;
use App\Infrastructure\Eloquent\User\StkDealerContent;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class DealerContentDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon           = 'heroicon-o-rectangle-stack';
    protected static string  $view                     = 'filament.pages.dealer-content-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string           $id     = null;
    public ?StkDealerContent $record = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = StkDealerContent::whereNull('deleted_at')->findOrFail($this->id);

        if ($this->record->dealer_id !== Auth::user()->dealer_id) {
            abort(403);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            DealerContentResource::getUrl() => 'サービス・イベント・保証',
            DealerContentDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return 'コンテンツ : [' . $this->record->title . ']';
    }

    public function infoList(): Infolist
    {
        $categoryMap = [
            'service'  => '各種サービス',
            'event'    => 'フェア＆イベント',
            'warranty' => '保証',
        ];

        $imageUrl = $this->record->image_path
            ? Storage::disk('s3')->url($this->record->image_path)
            : null;

        $data = [
            'id'          => $this->record->id,
            'category'    => $categoryMap[$this->record->category] ?? '-',
            'title'       => $this->record->title,
            'description' => $this->record->description ?? '-',
            'sort_order'  => $this->record->sort_order,
            'is_active'   => $this->record->is_active ? '表示' : '非表示',
            'started_at'  => $this->record->started_at?->format('Y/m/d') ?? '-',
            'ended_at'    => $this->record->ended_at?->format('Y/m/d') ?? '-',
            'image_url'   => $imageUrl,
            'created_at'  => $this->record->created_at,
            'updated_at'  => $this->record->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('画像')
                    ->schema([
                        TextEntry::make('image_url')
                            ->label('コンテンツ画像')
                            ->formatStateUsing(fn ($state) => $state
                                ? new HtmlString("<img src='{$state}' style='width:200px; height:150px; object-fit:cover; border-radius:8px;' />")
                                : '画像未登録'
                            ),
                    ]),

                Section::make('コンテンツ情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('category')->label('カテゴリ'),
                        TextEntry::make('title')->label('タイトル'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('サイト表示'),
                        TextEntry::make('started_at')->label('開始日'),
                        TextEntry::make('ended_at')->label('終了日'),
                        TextEntry::make('description')->label('概要・説明')->columnSpanFull(),
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
                ->url(DealerContentResource::getUrl('edit', ['record' => $this->record->id])),

            Action::make('delete')
                ->label('削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('コンテンツを削除しますか？')
                ->modalDescription('削除すると一覧から表示されなくなります。')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    $this->record->delete();

                    Notification::make()
                        ->title('削除しました')
                        ->success()
                        ->send();

                    $this->redirect(DealerContentResource::getUrl('index'));
                }),
        ];
    }
}
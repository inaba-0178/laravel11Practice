<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\DealerStaffResource;
use App\Infrastructure\Eloquent\User\StkDealerStaff;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\ImageEntry;

class DealerStaffDetail extends Page
{
    protected static ?string $navigationIcon           = 'heroicon-o-users';
    protected static string  $view                     = 'filament.pages.dealer-staff-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string        $id     = null;
    public ?StkDealerStaff $record = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = StkDealerStaff::whereNull('deleted_at')->findOrFail($this->id);

        if ($this->record->dealer_id !== Auth::user()->dealer_id) {
            abort(403);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            DealerStaffResource::getUrl() => 'スタッフ管理',
            DealerStaffDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return 'スタッフ : [' . $this->record->name . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'         => $this->record->id,
            'name'       => $this->record->name,
            'position'   => $this->record->position ?? '-',
            'comment'    => $this->record->comment ?? '-',
            'sort_order' => $this->record->sort_order,
            'is_active'  => $this->record->is_active ? '表示' : '非表示',
            'created_at' => $this->record->created_at,
            'updated_at' => $this->record->updated_at,
            'image_url' => $this->record->image_path
                ? \Illuminate\Support\Facades\Storage::disk('s3')->url($this->record->image_path)
                : null,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('スタッフ情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('スタッフ名'),
                        TextEntry::make('position')->label('役職'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('サイト表示'),
                        TextEntry::make('comment')->label('自己紹介・コメント')->columnSpanFull(),
                    ]),

                Section::make('日時')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
                Section::make('画像')
                    ->schema([
                        TextEntry::make('image_url')
                            ->label('スタッフ画像')
                            ->formatStateUsing(fn ($state) => $state
                                ? new \Illuminate\Support\HtmlString("<img src='{$state}' style='width:160px; height:160px; object-fit:cover; border-radius:8px;' />")
                                : '画像未登録'
                            ),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('編集')
                ->color('primary')
                ->url(DealerStaffResource::getUrl('edit', ['record' => $this->record->id])),

            Action::make('delete')
                ->label('削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('スタッフを削除しますか？')
                ->modalDescription('削除すると一覧から表示されなくなります。')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    $this->record->delete();

                    Notification::make()
                        ->title('削除しました')
                        ->success()
                        ->send();

                    $this->redirect(DealerStaffResource::getUrl('index'));
                }),
        ];
    }
}
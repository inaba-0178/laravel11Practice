<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstSeatOptionResource;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Illuminate\Http\Request;

class MstSeatOptionDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-seat-option-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int           $id            = null;
    public ?MstSeatOption $mstSeatOption = null;

    public function mount(Request $request): void
    {
        $this->id            = $request->input('id');
        $this->mstSeatOption = MstSeatOption::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstSeatOptionResource::getUrl()                       => 'シートオプション一覧',
            MstSeatOptionDetail::getUrl(['id' => $this->id])      => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return 'シートオプションID : [' . $this->mstSeatOption->id . '] ' . $this->mstSeatOption->label;
    }

    public function infoList(): Infolist
    {
        $s = $this->mstSeatOption;

        return Infolist::make()
            ->state([
                'id'         => $s->id,
                'label'      => $s->label,
                'value'      => $s->value,
                'sort_order' => $s->sort_order,
                'is_active'  => $s->is_active,
                'created_at' => $s->created_at,
                'updated_at' => $s->updated_at,
            ])
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('label')->label('ラベル'),
                        TextEntry::make('value')->label('値'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('利用可否')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? '利用可能' : '利用不可')
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}

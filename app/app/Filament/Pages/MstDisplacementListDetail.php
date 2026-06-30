<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstDisplacementListsResource;
use App\Infrastructure\Eloquent\Mst\MstDisplacementLists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Http\Request;

class MstDisplacementListDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-displacement-list-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int                  $id                  = null;
    public ?MstDisplacementLists $mstDisplacementList = null;

    public function mount(Request $request): void
    {
        $this->id                  = $request->input('id');
        $this->mstDisplacementList = MstDisplacementLists::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstDisplacementListsResource::getUrl()                         => '排気量一覧',
            MstDisplacementListDetail::getUrl(['id' => $this->id])         => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '排気量ID : [' . $this->mstDisplacementList->id . '] ' . $this->mstDisplacementList->name;
    }

    public function infoList(): Infolist
    {
        $p = $this->mstDisplacementList;

        return Infolist::make()
            ->state([
                'id'           => $p->id,
                'name'         => $p->name,
                'min_amount'   => $p->min_amount,
                'max_amount'   => $p->max_amount,
                'is_unlimited' => $p->is_unlimited,
                'created_at'   => $p->created_at,
                'updated_at'   => $p->updated_at,
            ])
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('排気量名'),
                        TextEntry::make('min_amount')->label('最小排気量'),
                        TextEntry::make('max_amount')->label('最大排気量'),
                        TextEntry::make('is_unlimited')->label('上限なし')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? '上限なし' : '上限あり')
                            ->color(fn($state) => $state ? 'warning' : 'gray'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstAreasResource;
use App\Infrastructure\Eloquent\Mst\MstAreas;

class MstAreaDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-area-detail';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?string $id = '';

    public function mount(Request $request)
    {
        $this->id = $request->input('id');
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstAreasResource::getUrl() => 'エリア一覧',
            MstAreaDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        $mstArea = $this->getQuery();
        return 'エリアID : [' . $mstArea->id . ']';
    }

    public function getQuery()
    {
        return MstAreas::query()
            ->where('id', $this->id)
            ->first();
    }

    public function infoList(): Infolist
    {
        $mstArea = $this->getQuery();

        $data = [
            'id'            => $mstArea->id,
            'name'          => $mstArea->name,
            'query_param'   => $mstArea->query_param,
            'sort_order'    => $mstArea->sort_order,
            'created_at'    => $mstArea->created_at,
            'updated_at'    => $mstArea->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('地方名'),
                        TextEntry::make('query_param')->label('地方URL'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ])
            ]);
    }

}

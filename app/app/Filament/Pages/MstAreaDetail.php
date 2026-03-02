<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstAreasResource;
use App\Infrastructure\Eloquent\Mst\MstAreas;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class MstAreaDetail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-area-detail';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstAreas $mstArea = null;

    public function mount(Request $request)
    {
        $this->id       = $request->input('id');
        $this->mstArea  = MstAreas::query()
            ->where('id', $this->id)
            ->first();
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
        return 'エリアID : [' . $this->mstArea->id . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'            => $this->mstArea->id,
            'name'          => $this->mstArea->name,
            'query_param'   => $this->mstArea->query_param,
            'sort_order'    => $this->mstArea->sort_order,
            'created_at'    => $this->mstArea->created_at,
            'updated_at'    => $this->mstArea->updated_at,
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

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MstRegions::query()->where('area_code', $this->id)
            )
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('name')->label('県名'),
            ])
            ->actions([
                Action::make('detail')
                    ->label('詳細'),
                    //ここはあとで修正 ->url(fn(MstRegions $record) => MstRegionDetail::getUrl(['id' => $record->id])),
            ]);
    }
}

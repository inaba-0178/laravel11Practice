<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstRegionsResource;
use App\Infrastructure\Eloquent\Mst\MstAreas;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use App\Domain\Common\Enums\AreaCode;
use App\Filament\Pages\MstAreaDetail;

class MstRegionDetail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-region-detail';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstRegions $mstRegion = null;
    public ?int $areaCode;

    public function mount(Request $request)
    {
        $this->id           = $request->input('id');
        $this->mstRegion    = MstRegions::query()
            ->where('id', $this->id)
            ->firstOrFail();
        $this->areaCode     = $this->mstRegion->area_code;
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstRegionsResource::getUrl() => '都道府県一覧',
            MstRegionDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '都道府県ID : [' . $this->mstRegion->id . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'            => $this->mstRegion->id,
            'area_code'     => AreaCode::tryFrom($this->mstRegion->area_code)?->label() ?? '',
            'name'          => $this->mstRegion->name,
            'url'           => $this->mstRegion->url,
            'query_param'   => $this->mstRegion->query_param,
            'sort_order'    => $this->mstRegion->sort_order,
            'created_at'    => $this->mstRegion->created_at,
            'updated_at'    => $this->mstRegion->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('area_code')->label('エリア'),
                        TextEntry::make('name')->label('地方名'),
                        TextEntry::make('URL')->label('URL'),
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
                MstAreas::query()->where('id', $this->areaCode)
            )
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('name')->label('エリア'),
            ])
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn(MstAreas $record) => MstAreaDetail::getUrl(['id' => $record->id])),
            ]);
    }
}

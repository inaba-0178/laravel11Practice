<?php

namespace App\Filament\Pages;

use App\Domain\Common\Enums\ProductionStatus;
use App\Filament\Pages\MstVehicleDetail;
use App\Filament\Resources\MstCarSeriesResource;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Http\Request;

class MstCarSeriesDetail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string     $view                       = 'filament.pages.mst-car-series-detail';
    protected static bool       $shouldRegisterNavigation   = false;
    protected static ?string    $title                      = '';

    public ?int             $seriesId;
    public ?MstCarSeries    $mstCarSeries = null;

    public function mount(Request $request)
    {
        $this->seriesId     = $request->input('series_id');
        $this->mstCarSeries = MstCarSeries::query()
            ->where('series_id', $this->seriesId)
            ->with([
                'mstCarSeriesBodyTypes.mstBodyType',
                'mstManufacturer',
            ])
            ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstCarSeriesResource::getUrl() => '車両一覧',
            MstCarSeriesDetail::getUrl(['series_id' => $this->seriesId]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '車両ID : [' . $this->mstCarSeries->series_id . '] ' . $this->mstCarSeries->series_name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MstVehicles::query()->where('series_id', $this->seriesId)->with('bodyType'))
            ->heading('紐づく車両マスタ一覧')
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('車両名')->sortable(),
                TextColumn::make('model_code')->label('モデルコード')->sortable(),
                TextColumn::make('bodyType.name')->label('ボディタイプ')->sortable(),
                TextColumn::make('status')->label('ステータス')
                    ->formatStateUsing(fn ($state) => $state ? (ProductionStatus::tryFrom($state)?->label() ?? $state) : null)
                    ->sortable(),
            ])
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn(MstVehicles $record) => MstVehicleDetail::getUrl([
                        'id'          => $record->id,
                        'from_series' => $this->seriesId,
                    ])),
            ], position: ActionsPosition::BeforeColumns)
            ->paginated(false);
    }

    public function infoList(): Infolist
    {
        $mstManufacturer    = $this->mstCarSeries->mstManufacturer;
        $bodyTypes          = $this->mstCarSeries->mstCarSeriesBodyTypes
                                ->pluck('mstBodyType.name')
                                ->join(', ');

        $data = [
            'series_id'                 => $this->mstCarSeries->series_id,
            'series_name'               => $this->mstCarSeries->series_name,
            'manufacturer_name'         => $mstManufacturer->name,
            'manufacturer_display_name' => $mstManufacturer->display_name,
            'body_types'                => $bodyTypes,
            'created_at'                => $this->mstCarSeries->created_at,
            'updated_at'                => $this->mstCarSeries->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('series_id')->label('車両ID'),
                        TextEntry::make('series_name')->label('車両名'),
                        TextEntry::make('manufacturer_name')->label('メーカー名'),
                        TextEntry::make('manufacturer_display_name')->label('メーカー表示名'),
                        TextEntry::make('body_types')->label('ボディタイプ')
                            ->columnSpan(2),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }

}

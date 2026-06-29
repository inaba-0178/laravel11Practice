<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstCarSeriesResource;
use App\Filament\Resources\MstVehiclesResource;
use App\Infrastructure\Eloquent\Mst\MstVehicleYearVersions;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Http\Request;

class MstVehicleDetail extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-vehicle-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int         $id;
    public ?int         $fromSeriesId = null;
    public ?MstVehicles $mstVehicle   = null;

    public function mount(Request $request): void
    {
        $this->id           = $request->input('id');
        $this->fromSeriesId = $request->integer('from_series') ?: null;
        $this->mstVehicle   = MstVehicles::with(['manufacturer', 'carSeries'])
            ->findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        if ($this->fromSeriesId) {
            return [
                MstCarSeriesResource::getUrl()                                                       => '車両一覧',
                MstCarSeriesDetail::getUrl(['series_id' => $this->fromSeriesId])                     => '詳細ページ',
                MstVehicleDetail::getUrl(['id' => $this->id, 'from_series' => $this->fromSeriesId]) => '車両バージョン選択',
            ];
        }

        return [
            MstVehiclesResource::getUrl()                 => '車両一覧',
            MstVehicleDetail::getUrl(['id' => $this->id]) => '車両バージョン選択',
        ];
    }

    public function getTitle(): string
    {
        return '車両ID : [' . $this->mstVehicle->id . '] ' . $this->mstVehicle->name;
    }

    public function infoList(): Infolist
    {
        return Infolist::make()
            ->state([
                'id'           => $this->mstVehicle->id,
                'name'         => $this->mstVehicle->name,
                'model_code'   => $this->mstVehicle->model_code,
                'body_type'    => $this->mstVehicle->body_type,
                'country_code' => $this->mstVehicle->country_code,
                'status'       => $this->mstVehicle->status,
                'series_name'  => $this->mstVehicle->carSeries?->series_name,
                'manufacturer' => $this->mstVehicle->manufacturer?->name,
                'created_at'   => $this->mstVehicle->created_at,
                'updated_at'   => $this->mstVehicle->updated_at,
            ])
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('車両名'),
                        TextEntry::make('manufacturer')->label('メーカー名'),
                        TextEntry::make('series_name')->label('車種シリーズ'),
                        TextEntry::make('model_code')->label('モデルコード'),
                        TextEntry::make('body_type')->label('ボディタイプ'),
                        TextEntry::make('country_code')->label('国コード'),
                        TextEntry::make('status')->label('ステータス'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MstVehicleYearVersions::query()->where('vehicle_id', $this->id))
            ->heading('年式バージョン一覧')
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('year_from')->label('年式（開始）')->sortable(),
                TextColumn::make('year_to')->label('年式（終了）')->sortable(),
                TextColumn::make('displacement_cc')->label('排気量(cc)')->sortable(),
                TextColumn::make('drive_type')->label('駆動方式')->sortable(),
                TextColumn::make('transmission_type')->label('ミッション')->sortable(),
                IconColumn::make('is_latest')->label('最新')->boolean()->sortable(),
            ])
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn(MstVehicleYearVersions $record) => MstVehicleYearVersionDetail::getUrl([
                        'id'          => $record->id,
                        'from_series' => $this->fromSeriesId,
                    ])),
            ], position: ActionsPosition::BeforeColumns)
            ->paginated(false);
    }
}

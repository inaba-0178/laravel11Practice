<?php

namespace App\Filament\Pages;

use App\Filament\Pages\MstCarSeriesDetail;
use App\Filament\Pages\MstVehicleDetail;
use App\Filament\Resources\MstCarSeriesResource;
use App\Filament\Resources\MstVehiclesResource;
use App\Domain\Common\Enums\ProductionStatus;
use App\Infrastructure\Eloquent\Mst\MstDisplacementList;
use App\Infrastructure\Eloquent\Mst\MstVehicleYearVersions;
use App\Infrastructure\Eloquent\Mst\MstVehicleTax;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Illuminate\Http\Request;

class MstVehicleYearVersionDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-vehicle-year-version-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int                     $id;
    public ?int                     $fromSeriesId = null;
    public ?MstVehicleYearVersions  $yearVersion  = null;
    public ?string                  $taxNormal    = null;
    public ?string                  $taxLight     = null;

    public function mount(Request $request): void
    {
        $this->id           = $request->input('id');
        $this->fromSeriesId = $request->integer('from_series') ?: null;
        $this->yearVersion  = MstVehicleYearVersions::with(['vehicle.manufacturer', 'vehicle.carSeries', 'vehicle.bodyType'])->findOrFail($this->id);
        $this->resolveTax();
    }

    private function resolveTax(): void
    {
        $displacementCc = $this->yearVersion->displacement_cc;
        if (!$displacementCc) {
            return;
        }

        $activeVersion = MstVersion::where('status', 'active')->latest('id')->first();
        if (!$activeVersion) {
            return;
        }

        $displacementList = MstDisplacementList::where('version_id', $activeVersion->id)
            ->where(function ($q) use ($displacementCc) {
                $q->where(function ($q) use ($displacementCc) {
                    $q->where('min_amount', '<=', $displacementCc)
                        ->where('max_amount', '>=', $displacementCc)
                        ->where('is_unlimited', false);
                })->orWhere(function ($q) use ($displacementCc) {
                    $q->where('is_unlimited', true)
                        ->where('min_amount', '<=', $displacementCc);
                });
            })
            ->first();

        if (!$displacementList) {
            return;
        }

        $taxes = MstVehicleTax::where('displacement_list_id', $displacementList->id)->get();

        $normal = $taxes->firstWhere('is_light', false);
        $light  = $taxes->firstWhere('is_light', true);

        $this->taxNormal = $normal ? number_format($normal->amount) . '円' : null;
        $this->taxLight  = $light  ? number_format($light->amount)  . '円' : null;
    }

    public function getBreadcrumbs(): array
    {
        $vehicleId = $this->yearVersion->vehicle_id;

        if ($this->fromSeriesId) {
            return [
                MstCarSeriesResource::getUrl()                                                                         => '車両一覧',
                MstCarSeriesDetail::getUrl(['series_id' => $this->fromSeriesId])                                       => '詳細ページ',
                MstVehicleDetail::getUrl(['id' => $vehicleId, 'from_series' => $this->fromSeriesId])                   => '車両バージョン選択',
                MstVehicleYearVersionDetail::getUrl(['id' => $this->id, 'from_series' => $this->fromSeriesId])         => '車両バージョン詳細',
            ];
        }

        return [
            MstVehiclesResource::getUrl()                            => '車両一覧',
            MstVehicleDetail::getUrl(['id' => $vehicleId])           => '車両バージョン選択',
            MstVehicleYearVersionDetail::getUrl(['id' => $this->id]) => '車両バージョン詳細',
        ];
    }

    public function getTitle(): string
    {
        $v = $this->yearVersion;
        return '年式ID : [' . $v->id . '] ' . ($v->year_from ?? '?') . '年 〜 ' . ($v->year_to ?? '現行');
    }

    public function infoList(): Infolist
    {
        $v = $this->yearVersion;
        return Infolist::make()
            ->state([
                'vehicle_name'         => $v->vehicle?->name,
                'vehicle_manufacturer' => $v->vehicle?->manufacturer?->name,
                'vehicle_series'       => $v->vehicle?->carSeries?->series_name,
                'vehicle_model_code'   => $v->vehicle?->model_code,
                'vehicle_body_type'    => $v->vehicle?->bodyType?->name,
                'vehicle_country_code' => $v->vehicle?->country_code,
                'vehicle_status'       => $v->vehicle?->status,
                'id'               => $v->id,
                'year_from'        => $v->year_from,
                'year_to'          => $v->year_to,
                'displacement_cc'  => $v->displacement_cc ? number_format($v->displacement_cc) . 'cc' : null,
                'drive_type'       => $v->drive_type,
                'fuel_efficiency_from' => $v->fuel_efficiency_from,
                'fuel_efficiency_to'   => $v->fuel_efficiency_to,
                'max_power_kw'     => $v->max_power_kw,
                'transmission_type'=> $v->transmission_type,
                'weight_kg'        => $v->weight_kg ? number_format($v->weight_kg) . 'kg' : null,
                'price_range_from' => $v->price_range_from ? number_format($v->price_range_from) . '円' : null,
                'price_range_to'   => $v->price_range_to  ? number_format($v->price_range_to)  . '円' : null,
                'is_latest'        => $v->is_latest ? '最新' : '旧版',
                'tax_normal'       => $this->taxNormal ?? '取得できません',
                'tax_light'        => $this->taxLight  ?? '取得できません',
                'created_at'       => $v->created_at,
                'updated_at'       => $v->updated_at,
            ])
            ->schema([
                Section::make('車両基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('vehicle_name')->label('車両名'),
                        TextEntry::make('vehicle_manufacturer')->label('メーカー'),
                        TextEntry::make('vehicle_series')->label('車種シリーズ'),
                        TextEntry::make('vehicle_model_code')->label('型式'),
                        TextEntry::make('vehicle_body_type')->label('ボディタイプ'),
                        TextEntry::make('vehicle_country_code')->label('国コード'),
                        TextEntry::make('vehicle_status')->label('ステータス')
                            ->formatStateUsing(fn ($state) => $state ? (ProductionStatus::tryFrom($state)?->label() ?? $state) : null),
                    ]),
                Section::make('年式バージョン情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('vehicle_name')->label('車両名'),
                        TextEntry::make('year_from')->label('年式（開始）'),
                        TextEntry::make('year_to')->label('年式（終了）'),
                        TextEntry::make('displacement_cc')->label('排気量'),
                        TextEntry::make('drive_type')->label('駆動方式'),
                        TextEntry::make('transmission_type')->label('ミッション'),
                        TextEntry::make('max_power_kw')->label('最大出力(kW)'),
                        TextEntry::make('fuel_efficiency_from')->label('燃費（下限）'),
                        TextEntry::make('fuel_efficiency_to')->label('燃費（上限）'),
                        TextEntry::make('price_range_from')->label('価格（下限）'),
                        TextEntry::make('price_range_to')->label('価格（上限）'),
                        TextEntry::make('is_latest')->label('最新フラグ'),
                        TextEntry::make('weight_kg')->label('車両重量'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
                Section::make('自動車税（アクティブバージョン）')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tax_normal')->label('普通車税額'),
                        TextEntry::make('tax_light')->label('軽自動車税額'),
                    ]),
            ]);
    }
}

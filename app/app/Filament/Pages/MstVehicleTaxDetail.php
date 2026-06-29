<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstVehicleTaxResource;
use App\Infrastructure\Eloquent\Mst\MstVehicleTax;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Http\Request;

class MstVehicleTaxDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-vehicle-tax-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int          $id      = null;
    public ?MstVehicleTax $tax    = null;

    public function mount(Request $request): void
    {
        $this->id  = $request->input('id');
        $this->tax = MstVehicleTax::with('displacementList')->findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstVehicleTaxResource::getUrl() => '自動車税一覧',
            MstVehicleTaxDetail::getUrl(['id' => $this->id]) => '自動車税詳細',
        ];
    }

    public function getTitle(): string
    {
        $t = $this->tax;
        return 'ID : [' . $t->id . '] ' . ($t->displacementList?->name ?? '排気量区分不明') . ' / ' . ($t->is_light ? '軽自動車' : '普通車');
    }

    public function infoList(): Infolist
    {
        $t = $this->tax;
        $d = $t->displacementList;

        return Infolist::make()
            ->state([
                'id'           => $t->id,
                'is_light'     => $t->is_light ? '軽自動車' : '普通車',
                'amount'       => number_format($t->amount) . '円',
                'created_at'   => $t->created_at,
                'updated_at'   => $t->updated_at,
                'disp_name'    => $d?->name,
                'disp_min'     => $d?->min_amount ? number_format($d->min_amount) . 'cc' : null,
                'disp_max'     => $d?->is_unlimited ? '上限なし' : ($d?->max_amount ? number_format($d->max_amount) . 'cc' : null),
                'disp_unlimited' => $d?->is_unlimited ? 'あり' : 'なし',
            ])
            ->schema([
                Section::make('自動車税情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('is_light')->label('車両区分'),
                        TextEntry::make('amount')->label('税額'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
                Section::make('排気量区分情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('disp_name')->label('区分名'),
                        TextEntry::make('disp_min')->label('排気量（下限）'),
                        TextEntry::make('disp_max')->label('排気量（上限）'),
                        TextEntry::make('disp_unlimited')->label('上限なし'),
                    ]),
            ]);
    }
}

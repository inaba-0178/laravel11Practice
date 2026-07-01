<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\MstVehicleWeightTaxResource;
use App\Infrastructure\Eloquent\Mst\MstVehicleWeightTax;

class MstVehicleWeightTaxDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon          = 'heroicon-o-document-text';
    protected static string  $view                    = 'filament.pages.mst-vehicle-weight-tax-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                   = '';

    public ?string $id;
    public ?MstVehicleWeightTax $record          = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = MstVehicleWeightTax::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstVehicleWeightTaxResource::getUrl() => '自動車重量税一覧',
            MstVehicleWeightTaxDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return '自動車重量税 ID : [' . $this->record->id . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'          => $this->record->id,
            'weight_from' => $this->record->weight_from . ' kg以上',
            'weight_to'   => $this->record->weight_to . ' kg未満',
            'is_light'    => $this->record->is_light ? '軽自動車' : '普通車',
            'amount'      => number_format((int)$this->record?->amount) . ' 円',
            'created_at'  => $this->record->created_at,
            'updated_at'  => $this->record->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('重量税情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('is_light')->label('車種区分'),
                        TextEntry::make('weight_from')->label('重量（以上）'),
                        TextEntry::make('weight_to')->label('重量（未満）'),
                        TextEntry::make('amount')->label('重量税'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}
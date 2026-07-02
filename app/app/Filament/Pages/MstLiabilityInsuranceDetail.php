<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\MstLiabilityInsuranceResource;
use App\Infrastructure\Eloquent\Mst\MstLiabilityInsurance;

class MstLiabilityInsuranceDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon           = 'heroicon-o-document-text';
    protected static string  $view                     = 'filament.pages.mst-liability-insurance-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string $id                           = null;
    public ?MstLiabilityInsurance $record        = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = MstLiabilityInsurance::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstLiabilityInsuranceResource::getUrl() => '自賠責保険料一覧',
            MstLiabilityInsuranceDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return '自賠責保険料 ID : [' . $this->record->id . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'           => $this->record->id,
            'vehicle_type' => $this->record->vehicle_type === 'light' ? '軽自動車' : '普通車',
            'months'       => $this->record->months . 'ヶ月',
            'amount'       => number_format((int) $this->record->amount) . ' 円',
            'created_at'   => $this->record->created_at,
            'updated_at'   => $this->record->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('自賠責保険料情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('vehicle_type')->label('車種区分'),
                        TextEntry::make('months')->label('保険期間'),
                        TextEntry::make('amount')->label('保険料'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}
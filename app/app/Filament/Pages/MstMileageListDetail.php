<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstMileageListsResource;
use App\Infrastructure\Eloquent\Mst\MstMileageLists;

class MstMileageListDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-mileage-list-detail';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstMileageLists $mstMileageList = null;

    public function mount(Request $request)
    {
        $this->id               = $request->input('id');
        $this->mstMileageList   = MstMileageLists::query()
                                    ->where('id', $this->id)
                                    ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstMileageListsResource::getUrl() => '走行距離一覧',
            MstMileageListDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '走行距離ID : [' . $this->mstMileageList->id . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'            => $this->mstMileageList->id,
            'name'          => $this->mstMileageList->name,
            'min_amount'    => $this->mstMileageList->min_amount,
            'max_amount'    => $this->mstMileageList->max_amount,
            'is_unlimited'  => $this->mstMileageList->is_unlimited,
            'created_at'    => $this->mstMileageList->created_at,
            'updated_at'    => $this->mstMileageList->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('走行距離'),
                        TextEntry::make('min_amount')->label('最低走行距離'),
                        TextEntry::make('max_amount')->label('最大走行距離'),
                        TextEntry::make('is_unlimited')->label('上限なし')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'あり' : 'なし')
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->columnSpan(2),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ])
            ]);
    }

}

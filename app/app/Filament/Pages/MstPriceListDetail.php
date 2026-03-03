<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstPriceListsResource;
use App\Infrastructure\Eloquent\Mst\MstPriceLists;

class MstPriceListDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-price-list-detail';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstPriceLists $mstPriceList = null;

    public function mount(Request $request)
    {
        $this->id           = $request->input('id');
        $this->mstPriceList = MstPriceLists::query()
            ->where('id', $this->id)
            ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstPriceListsResource::getUrl() => '価格帯一覧',
            MstPriceListDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '価格帯ID : [' . $this->mstPriceList->id . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'            => $this->mstPriceList->id,
            'name'          => $this->mstPriceList->name,
            'max_amount'    => $this->mstPriceList->max_amount,
            'is_unlimited'  => $this->mstPriceList->is_unlimited,
            'created_at'    => $this->mstPriceList->created_at,
            'updated_at'    => $this->mstPriceList->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('価格帯名'),
                        TextEntry::make('max_amount')->label('価格'),
                        TextEntry::make('is_unlimited')->label('最大価格フラグ'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ])
            ]);
    }

}

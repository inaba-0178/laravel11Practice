<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\MstCountriesResource;
use App\Infrastructure\Eloquent\Mst\MstCountries;

class MstCountryDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-country-detail';
    protected static bool   $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstCountries $mstCountry = null;

    public function mount(Request $request): void
    {
        $this->id         = $request->input('id');
        $this->mstCountry = MstCountries::query()
            ->where('id', $this->id)
            ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstCountriesResource::getUrl()                 => '国一覧',
            MstCountryDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '国ID : [' . $this->mstCountry->id . '] ' . $this->mstCountry->label;
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'           => $this->mstCountry->id,
            'country_code' => $this->mstCountry->country_code,
            'label'        => $this->mstCountry->label,
            'flag'         => $this->mstCountry->flag,
            'anchor'       => $this->mstCountry->anchor,
            'sort_order'   => $this->mstCountry->sort_order,
            'is_active'    => $this->mstCountry->is_active,
            'created_at'   => $this->mstCountry->created_at,
            'updated_at'   => $this->mstCountry->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('country_code')->label('国コード'),
                        TextEntry::make('label')->label('国名'),
                        TextEntry::make('flag')->label('国旗'),
                        TextEntry::make('anchor')->label('アンカー'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('利用可否')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? '利用可能' : '利用不可')
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->columnSpan('full'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}

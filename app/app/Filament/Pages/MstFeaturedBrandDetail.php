<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstFeaturedBrandsResource;
use App\Infrastructure\Eloquent\Mst\MstFeaturedBrands;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Http\Request;

class MstFeaturedBrandDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-featured-brand-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int               $id                 = null;
    public ?MstFeaturedBrands $mstFeaturedBrand   = null;

    public function mount(Request $request): void
    {
        $this->id               = $request->input('id');
        $this->mstFeaturedBrand = MstFeaturedBrands::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstFeaturedBrandsResource::getUrl()                        => '特集ブランド一覧',
            MstFeaturedBrandDetail::getUrl(['id' => $this->id])        => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '特集ブランドID : [' . $this->mstFeaturedBrand->id . '] ' . $this->mstFeaturedBrand->manufacturer_code;
    }

    public function infoList(): Infolist
    {
        $p = $this->mstFeaturedBrand;

        $positionLabel = match ($p->position) {
            'jp-top-row'     => '中古車',
            'import-top-row' => '輸入中古車',
            default          => $p->position,
        };

        return Infolist::make()
            ->state([
                'id'                => $p->id,
                'manufacturer_code' => $p->manufacturer_code,
                'position'          => $positionLabel,
                'sort_order'        => $p->sort_order,
                'is_active'         => $p->is_active,
                'created_at'        => $p->created_at,
                'updated_at'        => $p->updated_at,
            ])
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('manufacturer_code')->label('メーカーコード'),
                        TextEntry::make('position')->label('表示位置'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('有効')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? '有効' : '無効')
                            ->color(fn($state) => $state ? 'success' : 'gray'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}

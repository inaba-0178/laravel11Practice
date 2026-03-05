<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstManufacturersResource;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;

class MstManufacturerDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-manufacturer-detail';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstManufacturers $mstManufacturer = null;

    public function mount(Request $request)
    {
        $this->id = $request->input('id');
        $this->mstManufacturer = MstManufacturers::query()
            ->where('id', $this->id)
            ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstManufacturersResource::getUrl() => 'メーカー一覧',
            MstManufacturerDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return 'メーカーID : [' . $this->mstManufacturer->id . '] ' . $this->mstManufacturer->name;
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'            => $this->mstManufacturer->id,
            'name'          => $this->mstManufacturer->name,
            'name_kana'     => $this->mstManufacturer->name_kana,
            'display_name'  => $this->mstManufacturer->display_name,
            'code'          => $this->mstManufacturer->code,
            'url'           => $this->mstManufacturer->url,
            'description'   => $this->mstManufacturer->description,
            'country_code'  => $this->mstManufacturer->country_code,
            'sort_order'    => $this->mstManufacturer->sort_order,
            'is_active'     => $this->mstManufacturer->is_active,
            'created_at'    => $this->mstManufacturer->created_at,
            'updated_at'    => $this->mstManufacturer->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('メーカー名'),
                        TextEntry::make('name_kana')->label('メーカー名カナ'),
                        TextEntry::make('display_name')->label('表示名'),
                        TextEntry::make('code')->label('メーカーコード'),
                        TextEntry::make('country_code')->label('国コード'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('利用可否')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? '利用可能' : '利用不可')
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                        TextEntry::make('url')->label('URL')
                            ->columnSpan(2),
                        TextEntry::make('description')->label('説明')
                            ->columnSpan(2),
                        
                        
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ])
            ]);
    }

}

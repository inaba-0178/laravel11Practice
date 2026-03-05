<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstBodyTypesResource;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use Filament\Infolists\Components\ImageEntry;

class MstBodyTypeDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-body-type-detail';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstBodyTypes $mstBodyType = null;

    public function mount(Request $request)
    {
        $this->id = $request->input('id');
        $this->mstBodyType = MstBodyTypes::query()
            ->where('id', $this->id)
            ->with('image')
            ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstBodyTypesResource::getUrl() => 'メーカー一覧',
            MstBodyTypeDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return 'メーカーID : [' . $this->mstBodyType->id . '] ' . $this->mstBodyType->name;
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'                => $this->mstBodyType->id,
            'name'              => $this->mstBodyType->name,
            'name_kana'         => $this->mstBodyType->name_kana,
            'code'              => $this->mstBodyType->code,
            'description'       => $this->mstBodyType->description,
            'sort_order'        => $this->mstBodyType->sort_order,
            'is_active'         => $this->mstBodyType->is_active,
            'created_at'        => $this->mstBodyType->created_at,
            'updated_at'        => $this->mstBodyType->updated_at,
            //ここからmstBodyTypeImageのテーブルデータ
            'image_type'        => $this->mstBodyType->image?->image_type,
            'file_path'         => $this->mstBodyType->image?->file_path,
            'image_sort_order'  => $this->mstBodyType->image?->sort_order,
            'alt_text'          => $this->mstBodyType->image?->alt_text,
            'is_main'           => $this->mstBodyType->image?->is_main,
            'image_is_active'   => $this->mstBodyType->image?->is_active,
            'image_created_at'  => $this->mstBodyType->image?->created_at,
            'image_updated_at'  => $this->mstBodyType->image?->updated_at,
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
                        TextEntry::make('code')->label('メーカーコード'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_active')->label('利用可否')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? '利用可能' : '利用不可')
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                        TextEntry::make('description')->label('説明')
                            ->columnSpan(2),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
                    Section::make('画像情報')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('image_type')->label('画像種別'),
                            TextEntry::make('alt_text')->label('テキスト名'),
                            TextEntry::make('is_main')->label('メイン画像')
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? 'メイン' : 'その他')
                                ->color(fn ($state) => $state ? 'success' : 'gray'),
                            TextEntry::make('image_is_active')->label('利用可否')
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? '利用可能' : '利用不可')
                                ->color(fn ($state) => $state ? 'success' : 'gray'),
                            TextEntry::make('image_sort_order')->label('表示順')
                                ->columnSpan(2),
                            TextEntry::make('image_created_at')->label('作成日時'),
                            TextEntry::make('image_updated_at')->label('更新日時'),
                            ImageEntry::make('file_path')
                                ->label('画像')
                                ->disk('public')
                                ->columnSpan(2),
                            
                        ]),
            ]);
    }

}

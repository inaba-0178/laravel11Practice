<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\MstBasicOptionsResource;
use App\Infrastructure\Eloquent\Mst\MstBasicOptions;

class MstBasicOptionDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mst-basic-option-detail';
    protected static bool   $shouldRegisterNavigation = false;
    protected static ?string $title = '';

    public ?int $id;
    public ?MstBasicOptions $mstBasicOption = null;

    public function mount(Request $request): void
    {
        $this->id             = $request->input('id');
        $this->mstBasicOption = MstBasicOptions::query()
            ->where('id', $this->id)
            ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstBasicOptionsResource::getUrl()                => '基本オプション一覧',
            MstBasicOptionDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '基本オプションID : [' . $this->mstBasicOption->id . '] ' . $this->mstBasicOption->label;
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'           => $this->mstBasicOption->id,
            'label'        => $this->mstBasicOption->label,
            'value'        => $this->mstBasicOption->value,
            'is_highlight' => $this->mstBasicOption->is_highlight,
            'sort_order'   => $this->mstBasicOption->sort_order,
            'is_active'    => $this->mstBasicOption->is_active,
            'created_at'   => $this->mstBasicOption->created_at,
            'updated_at'   => $this->mstBasicOption->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('label')->label('ラベル'),
                        TextEntry::make('value')->label('値'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('is_highlight')->label('ハイライト')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'あり' : 'なし')
                            ->color(fn ($state) => $state ? 'warning' : 'gray'),
                        TextEntry::make('is_active')->label('利用可否')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? '利用可能' : '利用不可')
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}

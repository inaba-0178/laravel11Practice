<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstColorOptionsResource;
use App\Infrastructure\Eloquent\Mst\MstColorOptions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Http\Request;

class MstColorOptionsDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-color-options-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int             $id               = null;
    public ?MstColorOptions $mstColorOptions  = null;

    public function mount(Request $request): void
    {
        $this->id              = $request->input('id');
        $this->mstColorOptions = MstColorOptions::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstColorOptionsResource::getUrl()                       => 'カラーオプション一覧',
            MstColorOptionsDetail::getUrl(['id' => $this->id])      => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return 'カラーオプションID : [' . $this->mstColorOptions->id . '] ' . $this->mstColorOptions->label;
    }

    public function infoList(): Infolist
    {
        $p = $this->mstColorOptions;

        return Infolist::make()
            ->state([
                'id'         => $p->id,
                'value'      => $p->value,
                'label'      => $p->label,
                'hex_code'   => $p->hex_code,
                'group'      => $p->group,
                'sort_order' => $p->sort_order,
                'is_active'  => $p->is_active,
                'created_at' => $p->created_at,
                'updated_at' => $p->updated_at,
            ])
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('sort_order')->label('表示順'),
                        TextEntry::make('value')->label('値（APIキー）'),
                        TextEntry::make('label')->label('表示名'),
                        TextEntry::make('hex_code')
                            ->label('カラーコード')
                            ->html()
                            ->formatStateUsing(fn(?string $state) => $state
                                ? "<span style='display:inline-flex;align-items:center;gap:8px;'><span style='display:inline-block;width:24px;height:24px;border-radius:50%;background:{$state};border:1px solid #ccc;'></span>{$state}</span>"
                                : '-'),
                        TextEntry::make('group')->label('色系統'),
                        TextEntry::make('is_active')->label('有効')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? '有効' : '無効')
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}

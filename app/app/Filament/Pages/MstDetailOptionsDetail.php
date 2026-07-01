<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstDetailOptionsResource;
use App\Infrastructure\Eloquent\Mst\MstDetailOptions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Illuminate\Http\Request;

class MstDetailOptionsDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-detail-options-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int              $id                = null;
    public ?MstDetailOptions $mstDetailOptions  = null;

    public function mount(Request $request): void
    {
        $this->id               = $request->input('id');
        $this->mstDetailOptions = MstDetailOptions::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstDetailOptionsResource::getUrl()                      => '詳細オプション一覧',
            MstDetailOptionsDetail::getUrl(['id' => $this->id])     => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '詳細オプションID : [' . $this->mstDetailOptions->id . '] ' . $this->mstDetailOptions->label;
    }

    public function infoList(): Infolist
    {
        $p = $this->mstDetailOptions;

        return Infolist::make()
            ->state([
                'id'         => $p->id,
                'value'      => $p->value,
                'label'      => $p->label,
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

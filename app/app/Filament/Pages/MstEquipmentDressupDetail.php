<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstEquipmentDressupResource;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Illuminate\Http\Request;

class MstEquipmentDressupDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-equipment-dressup-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int                $id                  = null;
    public ?MstEquipmentDressup $mstEquipmentDressup = null;

    public function mount(Request $request): void
    {
        $this->id                  = $request->input('id');
        $this->mstEquipmentDressup = MstEquipmentDressup::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstEquipmentDressupResource::getUrl()                       => '装備（ドレスアップ）一覧',
            MstEquipmentDressupDetail::getUrl(['id' => $this->id])      => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '装備（ドレスアップ）ID : [' . $this->mstEquipmentDressup->id . '] ' . $this->mstEquipmentDressup->label;
    }

    public function infoList(): Infolist
    {
        $p = $this->mstEquipmentDressup;

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

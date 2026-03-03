<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Http\Request;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\MstRidingCapacityListsResource;
use App\Infrastructure\Eloquent\Mst\MstRidingCapacityLists;

class MstRidingCapacityListDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string     $view = 'filament.pages.mst-riding-capacity-list-detail';
    protected static bool       $shouldRegisterNavigation = false;
    protected static ?string    $title = '';

    public ?int $id;
    public ?MstRidingCapacityLists $mstRidingCapacityList = null;

    public function mount(Request $request)
    {
        $this->id = $request->input('id');

        $this->mstRidingCapacityList = MstRidingCapacityLists::query()
            ->where('id', $this->id)
            ->firstOrFail();
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstRidingCapacityListsResource::getUrl() => '乗車定員一覧',
            MstRidingCapacityListDetail::getUrl(['id' => $this->id]) => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return '乗車定員ID : [' . $this->mstRidingCapacityList->id . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'            => $this->mstRidingCapacityList->id,
            'name'          => $this->mstRidingCapacityList->name,
            'max_amount'    => $this->mstRidingCapacityList->max_amount,
            'is_unlimited'  => $this->mstRidingCapacityList->is_unlimited,
            'created_at'    => $this->mstRidingCapacityList->created_at,
            'updated_at'    => $this->mstRidingCapacityList->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('乗車定員'),
                        TextEntry::make('max_amount')->label('最大数'),
                        TextEntry::make('is_unlimited')->label('最大乗車定員数フラグ'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ])
            ]);
    }

}

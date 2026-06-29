<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MstLoanPlanResource;
use App\Infrastructure\Eloquent\Mst\MstLoanPlan;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Http\Request;

class MstLoanPlanDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string  $view           = 'filament.pages.mst-loan-plan-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title          = '';

    public ?int         $id            = null;
    public ?MstLoanPlan $mstLoanPlan   = null;

    public function mount(Request $request): void
    {
        $this->id          = $request->input('id');
        $this->mstLoanPlan = MstLoanPlan::findOrFail($this->id);
    }

    public function getBreadcrumbs(): array
    {
        return [
            MstLoanPlanResource::getUrl()                    => 'ローンプラン一覧',
            MstLoanPlanDetail::getUrl(['id' => $this->id])   => '詳細ページ',
        ];
    }

    public function getTitle(): string
    {
        return 'ローンプランID : [' . $this->mstLoanPlan->id . '] ' . $this->mstLoanPlan->name;
    }

    public function infoList(): Infolist
    {
        $p = $this->mstLoanPlan;

        return Infolist::make()
            ->state([
                'id'            => $p->id,
                'name'          => $p->name,
                'interest_rate' => $p->interest_rate . '%',
                'months_options'=> implode('・', array_map(fn($m) => $m . '回', $p->months_options ?? [])),
                'min_months'    => $p->min_months . '回',
                'max_months'    => $p->max_months . '回',
                'is_default'    => $p->is_default,
                'is_active'     => $p->is_active,
                'created_at'    => $p->created_at,
                'updated_at'    => $p->updated_at,
            ])
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('プラン名'),
                        TextEntry::make('interest_rate')->label('金利'),
                        TextEntry::make('months_options')->label('回数選択肢'),
                        TextEntry::make('min_months')->label('最小回数'),
                        TextEntry::make('max_months')->label('最大回数'),
                        TextEntry::make('is_default')->label('デフォルト')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'デフォルト' : '非デフォルト')
                            ->color(fn ($state) => $state ? 'warning' : 'gray'),
                        TextEntry::make('is_active')->label('有効')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? '有効' : '無効')
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }
}

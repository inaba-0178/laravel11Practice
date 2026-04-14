<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Illuminate\Http\Request;
use App\Filament\Resources\DealerShopResource;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Infrastructure\Eloquent\Mst\MstAreas;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class DealerShopDetail extends Page
{
    protected static ?string $navigationIcon           = 'heroicon-o-building-storefront';
    protected static string  $view                     = 'filament.pages.dealer-shop-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string        $id     = null;
    public ?StkCarDealer  $record = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = StkCarDealer::whereNull('deleted_at')->findOrFail($this->id);

        if ($this->record->id !== Auth::user()->dealer_id) {
            abort(403);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            DealerShopResource::getUrl() => '店舗情報',
            DealerShopDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return '店舗情報 : [' . $this->record->name . ']';
    }

    public function infoList(): Infolist
    {
        $region = MstRegions::find($this->record->region_id);
        $area   = MstAreas::find($this->record->area_code);

        $dealerTypeMap = [
            'new_car'  => '新車',
            'used_car' => '中古車',
            'both'     => '両方',
        ];

        $loanStatus = match(true) {
            $this->record->loan_setting_enabled == 1                => '承認済み',
            $this->record->isLoanSettingPending()                   => '申請中',
            !empty($this->record->loan_setting_rejected_reason)     => '却下',
            default                                                 => '未申請',
        };

        $data = [
            'id'                => $this->record->id,
            'name'              => $this->record->name,
            'postal_code'       => $this->record->postal_code,
            'region'            => $region?->name ?? '-',
            'area'              => $area?->name ?? '-',
            'city'              => $this->record->city,
            'address_detail'    => $this->record->address_detail ?? '-',
            'phone'             => $this->record->phone ?? '-',
            'email'             => $this->record->email ?? '-',
            'website_url'       => $this->record->website_url ?? '-',
            'business_hours'    => ($this->record->business_hours_from && $this->record->business_hours_to)
                                        ? $this->record->business_hours_from . '〜' . $this->record->business_hours_to
                                    : '-',

            'regular_holiday'   => (function () {
                                        if (!$this->record->regular_holiday_days) return '-';
                                            $suffix = $this->record->regular_holiday_except_holiday ? '（祝日除く）' : '';
                                            return $this->record->regular_holiday_days . $suffix;
                                    })(),
            'dealer_type'       => $dealerTypeMap[$this->record->dealer_type] ?? '-',
            'free_text'         => $this->record->free_text ?? '-',
            'is_active'         => $this->record->is_active ? '公開中' : '非公開',
            'review_rating'     => $this->record->review_rating ?? '-',
            'review_count'      => $this->record->review_count . '件',
            'loan_status'       => $loanStatus,
            'created_at'        => $this->record->created_at,
            'updated_at'        => $this->record->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('基本情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('店舗名'),
                        TextEntry::make('postal_code')->label('郵便番号'),
                        TextEntry::make('region')->label('都道府県'),
                        TextEntry::make('area')->label('地方'),
                        TextEntry::make('city')->label('市区町村'),
                        TextEntry::make('address_detail')->label('番地・建物'),
                        TextEntry::make('dealer_type')->label('ディーラー種別'),
                    ]),

                Section::make('連絡先')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('phone')->label('電話番号'),
                        TextEntry::make('email')->label('メールアドレス'),
                        TextEntry::make('website_url')->label('ホームページURL'),
                    ]),

                Section::make('営業情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('business_hours')->label('営業時間'),
                        TextEntry::make('regular_holiday')->label('定休日'),
                        TextEntry::make('free_text')->label('フリーテキスト')->columnSpanFull(),
                    ]),

                Section::make('ステータス')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('is_active')->label('公開状況'),
                        TextEntry::make('review_rating')->label('クチコミ評価'),
                        TextEntry::make('review_count')->label('クチコミ件数'),
                        TextEntry::make('loan_status')->label('ローン設定申請状況'),
                    ]),

                Section::make('日時')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('編集')
                ->color('primary')
                ->url(DealerShopResource::getUrl('edit', ['record' => $this->record->id])),
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DealerShopResource\Pages\ListDealerShops;
use App\Filament\Resources\DealerShopResource\Pages\EditDealerShop;
use App\Filament\Pages\DealerShopDetail;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Infrastructure\Eloquent\Mst\MstAreas;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use App\Constants\DealerEgularHolidayDayConstants;
use App\Constants\DealerTypesConstants;
use App\Domain\Common\Services\GeocodingService;

class DealerShopResource extends Resource
{
    protected static ?string $model            = StkCarDealer::class;
    protected static ?string $navigationIcon   = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::DEALER_SHOP->value;
    protected static ?string $pluralModelLabel = '店舗情報';
    protected static ?string $modelLabel       = '店舗情報';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['dealer', 'dealer_staff']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', Auth::user()?->dealer_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('店舗名')
                ->required()
                ->maxLength(255),

            TextInput::make('postal_code')
                ->label('郵便番号')
                ->nullable()
                ->maxLength(8)
                ->placeholder('例：1234567')
                ->regex('/^\d{7}$|^\d{3}-\d{4}$/')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if (!$state) return;

                    $geocoding = new GeocodingService();
                    $result    = $geocoding->resolveFromPostalCode($state);
                    if (!$result) return;

                    $region = MstRegions::where('name', 'like', '%' . $result['address1'] . '%')->first();

                    if ($region) {
                        $set('area_code', $region->area_code);
                        // area_codeのreactive完了後にregion_idをセット
                        $set('region_id', null);
                        $set('region_id', $region->id);
                    }

                    $set('city', $result['address2'] . $result['address3']);
                    $set('latitude',  $result['latitude']);
                    $set('longitude', $result['longitude']);
                }),

            TextInput::make('latitude')
                ->label('緯度')
                ->numeric()
                ->nullable()
                ->step(0.0000001),

            TextInput::make('longitude')
                ->label('経度')
                ->numeric()
                ->nullable()
                ->step(0.0000001),

            Select::make('area_code')
                ->label('地方')
                ->options(
                    MstAreas::orderBy('sort_order')->pluck('name', 'id')
                )
                ->required()
                ->reactive(),

            Select::make('region_id')
                ->label('都道府県')
                ->options(function (callable $get) {
                    $areaCode = $get('area_code');
                    if (!$areaCode) return [];
                    return MstRegions::where('area_code', $areaCode)
                        ->orderBy('sort_order')
                        ->pluck('name', 'id');
                })
                ->required()
                ->reactive()
                ->disabled(fn (callable $get) => !$get('area_code'))
                ->dehydrated(true)
                ->afterStateUpdated(fn ($state) => $state),

            TextInput::make('city')
                ->label('市区町村')
                ->required()
                ->maxLength(100),

            TextInput::make('address_detail')
                ->label('番地・建物')
                ->nullable()
                ->maxLength(255),

            TextInput::make('phone')
                ->label('電話番号')
                ->nullable()
                ->maxLength(20),

            TextInput::make('email')
                ->label('メールアドレス')
                ->email()
                ->nullable()
                ->maxLength(255),

            TextInput::make('website_url')
                ->label('ホームページURL')
                ->url()
                ->nullable()
                ->maxLength(500)
                ->columnSpanFull(),

            Select::make('business_hours_from')
                ->label('営業開始時間')
                ->options(self::getTimeOptions())
                ->nullable(),

            Select::make('business_hours_to')
                ->label('営業終了時間')
                ->options(self::getTimeOptions())
                ->nullable(),

            CheckboxList::make('regular_holiday_days')
                ->label('定休日')
                ->options(DealerEgularHolidayDayConstants::REGULAR_HOLIDAY_DAYS)
                ->columns(4)
                ->afterStateHydrated(function (CheckboxList $component, $state) {
                    if (is_string($state) && !empty($state)) {
                        $component->state(explode(',', $state));
                    } elseif (empty($state)) {
                        $component->state([]);
                    }
                })
                ->dehydrateStateUsing(fn ($state) => is_array($state) ? implode(',', $state) : null),

            Toggle::make('regular_holiday_except_holiday')
                ->label('祝日除く')
                ->default(false),

            Select::make('dealer_type')
                ->label('ディーラー種別')
                ->options(DealerTypesConstants::DEALER_TYPES)
                ->required(),

            Textarea::make('free_text')
                ->label('フリーテキスト')
                ->nullable()
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('店舗名')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('電話番号'),

                TextColumn::make('business_hours_from')
                    ->label('営業時間')
                    ->formatStateUsing(function ($state, StkCarDealer $record) {
                        if (!$record->business_hours_from || !$record->business_hours_to) return '-';
                        return $record->business_hours_from . '〜' . $record->business_hours_to;
                    }),

                TextColumn::make('regular_holiday_days')
                    ->label('定休日')
                    ->formatStateUsing(function ($state, StkCarDealer $record) {
                        if (!$record->regular_holiday_days) return '-';
                        $suffix = $record->regular_holiday_except_holiday ? '（祝日除く）' : '';
                        return $record->regular_holiday_days . $suffix;
                    }),

                IconColumn::make('is_active')
                    ->label('公開中')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('更新日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (StkCarDealer $record) => DealerShopDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDealerShops::route('/'),
            'edit'  => EditDealerShop::route('/{record}/edit'),
        ];
    }

    private static function getTimeOptions(): array
    {
        $options = [];
        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 10) {
                $time           = sprintf('%02d:%02d', $hour, $minute);
                $options[$time] = $time;
            }
        }
        return $options;
    }
}
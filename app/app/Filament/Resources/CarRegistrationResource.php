<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use App\Infrastructure\Eloquent\Mst\MstVehicleYearVersions;
use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkDealerFee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use App\Filament\Resources\CarRegistrationResource\Pages\ListCarRegistrations;
use App\Filament\Resources\CarRegistrationResource\Pages\CreateCarRegistration;
use App\Filament\Resources\CarRegistrationResource\Pages\EditCarRegistration;
use Filament\Tables\Columns\TextColumn;
use App\Constants\CarStatus;
use App\Constants\CarOptionCategory;
use App\Constants\SlideDoor;
use App\Constants\FuelType;
use App\Constants\PriceDisplayType;
use App\Domain\Shared\Enums\RepairHistory;
use App\Constants\DriveSystem;
use App\Constants\InspectionStatus;
use App\Constants\Transmission;
use App\Constants\SteeringWheel;
use Filament\Forms\Components\Grid;
use App\Constants\LoanMonths;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Eloquent\User\StkDealerLoanPlan;
use App\Infrastructure\Eloquent\Mst\MstLoanPlan;
use App\Constants\LoanPlanLabel;

class CarRegistrationResource extends Resource
{
    use HasResourcePermission;
 
    protected static ?string    $model              = StkCar::class;
    protected static ?string    $navigationIcon     = 'heroicon-o-truck';
    protected static ?string    $navigationGroup    = NavigationGroup::DEALER_GROUP->value;
    protected static ?int       $navigationSort     = NavigationSort::CAR_REGISTRATION->value;
    protected static ?string    $pluralModelLabel   = '車両登録';
    protected static ?string    $modelLabel         = '車両登録';
    
    // dealer/dealer_staffのみアクセス可能

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ===== ①メーカー・車体名・年式 =====
            Section::make('メーカー・車体名・年式')
                ->schema([
                    Select::make('manufacturer_id')
                        ->label('メーカー')
                        ->options(
                            MstManufacturers::where('is_active', 1)
                                ->orderBy('sort_order')
                                ->pluck('display_name', 'id')
                        )
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set) {
                            $set('series_id', null);
                            $set('vehicle_id', null);
                            $set('year_version_id', null);
                            $set('model_year', null);
                        }),

                    Select::make('series_id')
                        ->label('車体名')
                        ->options(function (Get $get) {
                            $manufacturerId = $get('manufacturer_id');
                            if (!$manufacturerId)
                            {
                                return [];
                            }
                            return MstCarSeries::where('manufacturer_id', $manufacturerId)
                                ->orderBy('series_name')
                                ->pluck('series_name', 'series_id');
                        })
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set) {
                            $set('vehicle_id', null);
                            $set('year_version_id', null);
                            $set('model_year', null);
                        })
                        ->disabled(fn (Get $get) => !$get('manufacturer_id')),

                    Select::make('vehicle_id')
                        ->label('年式・グレード')
                        ->options(function (Get $get) {
                            $seriesId = $get('series_id');
                            if (!$seriesId)
                            {
                                return [];
                            }
                            return MstVehicles::where('series_id', $seriesId)
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            // 年式選択リセット
                            $set('year_version_id', null);
                            $set('model_year', null);
                        })
                        ->disabled(fn (Get $get) => !$get('series_id')),

                    Select::make('model_year')
                        ->label('年式')
                        ->options(function (Get $get) {
                            $vehicleId = $get('vehicle_id');
                            if (!$vehicleId) return [];

                            $version = MstVehicleYearVersions::where('vehicle_id', $vehicleId)->first();
                            if (!$version) return [];

                            $yearTo = $version->year_to ?? now()->year;

                            return collect(range($version->year_from, $yearTo))
                                ->mapWithKeys(fn ($year) => [$year => "{$year}年"])
                                ->toArray();
                        })
                        ->required()
                        ->live()
                        ->disabled(fn (Get $get) => !$get('vehicle_id'))
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $vehicleId = $get('vehicle_id');
                            if (!$vehicleId) return;

                            $version = MstVehicleYearVersions::where('vehicle_id', $vehicleId)->first();
                            if (!$version) return;

                            $set('displacement', $version->displacement_cc);
                            $set('drive_system', $version->drive_type);
                            $set('transmission', $version->transmission_type);
                        }),
                ])
                ->columns(2),

            // ===== ②車両基本情報 =====
            Section::make('車両基本情報')
                ->schema([
                    TextInput::make('price')
                        ->label('支払価格（円）')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    Select::make('price_display_type')
                        ->label('価格表示方法')
                        ->options(
                            PriceDisplayType::LABELS
                        )
                        ->default('actual')
                        ->required(),

                    TextInput::make('mileage')
                        ->label('走行距離（km）')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    TextInput::make('color')
                        ->label('ボディカラー')
                        ->required()
                        ->maxLength(100),
                    
                    Select::make('color_group')
                        ->label('色系統')
                        ->options([
                            'white'  => '白系',
                            'black'  => '黒系',
                            'silver' => '銀系',
                            'red'    => '赤系',
                            'blue'   => '青系',
                            'green'  => '緑系',
                            'brown'  => '茶系',
                            'yellow' => '黄系',
                            'pink'   => 'ピンク系',
                            'other'  => 'その他',
                        ])
                        ->required(),

                    Select::make('repair_history')
                        ->label('修復歴')
                        ->options(
                            RepairHistory::LABELS
                        )
                        ->default('unknown')
                        ->required(),

                    Select::make('fuel_type')
                        ->label('燃料タイプ')
                        ->options(
                            FuelType::LABELS
                        ),

                    Select::make('body_type_id')
                        ->label('ボディタイプ')
                        ->options(
                            MstBodyTypes::orderBy('name')
                                ->pluck('name', 'id')
                        ),

                    Select::make('region_id')
                        ->label('地域（都道府県）')
                        ->options(
                            MstRegions::orderBy('sort_order')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->required(),
                    // フラグ系
                    Fieldset::make('車両の特徴')
                        ->schema([
                            Checkbox::make('special_one_owner')
                                ->label('ワンオーナー')
                                ->default(false),
                            Checkbox::make('special_camping_car')
                                ->label('キャンピングカー')
                                ->default(false),
                            Checkbox::make('special_welfare_car')
                                ->label('福祉車両')
                                ->default(false),
                            Checkbox::make('special_unused')
                                ->label('登録済未使用車')
                                ->default(false),
                            Checkbox::make('special_eco_car')
                                ->label('エコカー減税対象')
                                ->default(false),
                            Checkbox::make('special_unregistered')
                                ->label('未登録車')
                                ->default(false),
                        ])
                        ->columns(3),

                    Fieldset::make('販売・サービス情報')
                        ->schema([
                            Checkbox::make('opt_quality_cert')
                                ->label('車両品質評価書付き')
                                ->default(false),
                            Checkbox::make('opt_purchase_plan')
                                ->label('購入プラン付き')
                                ->default(false),
                            Checkbox::make('opt_sensor_after')
                                ->label('アフター保証対象車')
                                ->default(false),
                            Checkbox::make('opt_online_consult')
                                ->label('オンライン相談可')
                                ->default(false),
                        ])
                        ->columns(2),

                    TextInput::make('recycle_fee')
                        ->label('リサイクル預託金（円）')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->rules(['integer', 'min:0'])
                        ->nullable(),
                    Select::make('dealer_fee_id')
                        ->label('諸費用プラン')
                        ->options(function () {
                            $dealerId = Auth::user()?->dealer_id;
                            return StkDealerFee::where('dealer_id', $dealerId)
                                ->whereNull('deleted_at')
                                ->get()
                                ->mapWithKeys(fn ($fee) => [
                                    $fee->id => "{$fee->name}（登録:{$fee->registration_fee}円 車庫:{$fee->garage_cert_fee}円 納車:{$fee->delivery_fee}円 整備:{$fee->maintenance_fee}円）"
                                ])
                                ->toArray();
                        })
                        ->default(function () {
                            $dealerId = Auth::user()?->dealer_id;
                            return StkDealerFee::where('dealer_id', $dealerId)
                                ->where('is_default', true)
                                ->whereNull('deleted_at')
                                ->value('id');
                        })
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ===== ③車両詳細・スペック（stk_car_details） =====
            Section::make('車両スペック')
                ->schema([
                    DatePicker::make('first_registration_date')
                        ->label('初回登録日'),

                    DatePicker::make('inspection_expire_date')
                        ->label('車検満了日'),

                    Select::make('inspection_status')
                        ->label('車検状態')
                        ->options(
                            InspectionStatus::LABELS
                        )
                        ->default('available')
                        ->required(),

                    Select::make('drive_system')
                        ->label('駆動方式')
                        ->options(
                            DriveSystem::LABELS
                        ),

                    TextInput::make('displacement')
                        ->label('排気量（cc）')
                        ->numeric()
                        ->minValue(0),

                    Select::make('transmission')
                        ->label('ミッション')
                        ->options(
                            Transmission::LABELS
                        ),

                    Select::make('steering_wheel')
                        ->label('ハンドル')
                        ->options(
                            SteeringWheel::LABELS
                        )
                        ->default('right')
                        ->required(),

                    TextInput::make('number_of_doors')
                        ->label('ドア数')
                        ->numeric()
                        ->minValue(2)
                        ->maxValue(7)
                        ->required(),

                    Select::make('slide_door')
                        ->label('スライドドア')
                        ->options(
                            SlideDoor::LABELS
                        )
                        ->default('none')
                        ->required(),

                    TextInput::make('riding_capacity')
                        ->label('乗車定員')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(99),

                    Toggle::make('loan_available')
                        ->label('ローン可'),

                    Textarea::make('description')
                        ->label('車両説明文')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ===== ローン設定セクション =====
            Section::make('ローン設定')
                ->collapsible()
                ->schema(function () {
                    $dealer      = StkCarDealer::find(auth()->user()->dealer_id);
                    $defaultPlan = MstLoanPlan::getDefault();
            
                    // システムデフォルトは 'mst_{id}' をキーにして区別
                    $defaultOption = $defaultPlan
                        ? ["mst_{$defaultPlan->id}" => "システムデフォルト（{$defaultPlan->interest_rate}% / {$defaultPlan->min_months}〜{$defaultPlan->max_months}回）"]
                        : [];
            
                    // 許可ありの場合はディーラープランも追加 'dealer_{uuid}' をキーにして区別
                    $dealerOptions = [];
                    if ($dealer && $dealer->loan_setting_enabled) {
                        $dealerOptions = StkDealerLoanPlan::where('dealer_id', $dealer->id)
                            ->where('is_active', 1)
                            ->whereNull('deleted_at')
                            ->get()
                            ->mapWithKeys(fn ($plan) => [
                                "{$plan->id}" => "{$plan->name}（{$plan->interest_rate}% / {$plan->min_months}〜{$plan->max_months}回）"
                            ])
                            ->toArray();
                    }
            
                    $planOptions = array_merge($defaultOption, $dealerOptions);
            
                    return [
                        Repeater::make('loans')
                            ->label('ローンプラン')
                            ->relationship('loans')
                            ->schema([
                                Select::make('dealer_loan_plan_id')
                                    ->label('プランを選択')
                                    ->options($planOptions)
                                    ->required()
                                    ->columnSpanFull()
                                    ->afterStateHydrated(function ($state, $set) use ($defaultPlan) {
                                        // dealer_loan_plan_idがnullの場合はシステムデフォルトキーに変換
                                        if ($state === null && $defaultPlan) {
                                            $set('dealer_loan_plan_id', "mst_{$defaultPlan->id}");
                                        }
                                    }),
                                ])
                            ->addActionLabel('＋ プランを追加')
                            ->maxItems(3)
                            ->columnSpanFull(),
                    ];
                }),
                
            // ===== ④装備仕様（マスタから取得） =====
            Section::make('装備仕様')
                ->schema([
                    // 安全装備
                    Fieldset::make('安全装備')
                        ->schema(
                            self::buildEquipmentCheckboxes('safety', MstEquipmentSafety::class)
                        )
                        ->columns(4),

                    // 快適装備
                    Fieldset::make('快適装備')
                        ->schema(
                            self::buildEquipmentCheckboxes('basic', MstEquipmentBasic::class)
                        )
                        ->columns(4),

                    // インテリア
                    Fieldset::make('インテリア')
                        ->schema(
                            self::buildEquipmentCheckboxes('seat', MstSeatOption::class)
                        )
                        ->columns(4),

                    // エクステリア
                    Fieldset::make('エクステリア')
                        ->schema(
                            self::buildEquipmentCheckboxes('dress_up', MstEquipmentDressup::class)
                        )
                        ->columns(4),

                    // 環境装備
                    Fieldset::make('環境装備')
                        ->schema(
                            self::buildEquipmentCheckboxes('environmental', MstEquipmentEnv::class)
                        )
                        ->columns(4),

                    Fieldset::make('オーディオ・ナビ')
                        ->schema([
                            Checkbox::make('audio_cd')
                                ->label('CD再生')
                                ->default(false),
                            Checkbox::make('audio_dvd')
                                ->label('DVD再生')
                                ->default(false),
                            Checkbox::make('audio_bluetooth')
                                ->label('Bluetooth')
                                ->default(false),
                            Checkbox::make('audio_usb')
                                ->label('USB')
                                ->default(false),
                            TextInput::make('audio_maker')
                                ->label('オーディオメーカー')
                                ->placeholder('例：パイオニア、ケンウッド')
                                ->maxLength(100)
                                ->columnSpanFull(),
                            Checkbox::make('navi_navi')
                                ->label('カーナビあり')
                                ->default(false),
                            Checkbox::make('navi_tv')
                                ->label('TVあり')
                                ->default(false),
                            Checkbox::make('navi_dvd')
                                ->label('DVDナビあり')
                                ->default(false),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ]),

            // ===== ⑤その他オプション（動的行追加） =====
            Section::make('その他オプション')
                ->schema([
                    Repeater::make('other_options')
                        ->label('')
                        ->schema([
                            Select::make('option_category')
                                ->label('カテゴリ')
                                ->options(
                                    CarOptionCategory::LABELS
                                )
                                ->required(),

                            TextInput::make('option_name')
                                ->label('オプション名')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(2)
                        ->addActionLabel('＋ オプションを追加')
                        ->defaultItems(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                StkCar::query()->where('dealer_id', Auth::user()?->dealer_id)
            )
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('series.series_name')->label('車体名'),
                TextColumn::make('price')->label('価格')->money('JPY'),
                TextColumn::make('status')
                    ->label('ステータス')
                    ->badge()
                    ->color(
                        fn (string $state) => CarStatus::COLORS[$state] ?? 'gray'
                    )
                    ->formatStateUsing(
                        fn (string $state) => CarStatus::LABELS[$state] ?? $state                    
                    ),
                TextColumn::make('created_at')->label('登録日時')->dateTime('Y/m/d H:i'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCarRegistrations::route('/'),
            'create' => CreateCarRegistration::route('/create'),
            'edit'   => EditCarRegistration::route('/{record}/edit'),
        ];
    }
    

    // ===== 装備仕様チェックボックス生成ヘルパー =====
    private static function buildEquipmentCheckboxes(string $category, string $modelClass): array
    {
        return $modelClass::where('is_active', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(
                fn ($item) => Checkbox::make("equipment_{$category}_{$item->value}")
                    ->label($item->label)
                    ->default(false),
            )
            ->toArray();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('保存'),

            Actions\CancelAction::make()
                ->label('キャンセル'),
        ];
    }
}
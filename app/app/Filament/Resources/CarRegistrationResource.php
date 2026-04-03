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
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
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
use App\Constants\RepairHistory;
use App\Constants\DriveSystem;
use App\Constants\InspectionStatus;
use App\Constants\Transmission;
use App\Constants\SteeringWheel;
use Filament\Forms\Components\Grid;

class CarRegistrationResource extends Resource
{
 
    protected static ?string    $model              = StkCar::class;
    protected static ?string    $navigationIcon     = 'heroicon-o-truck';
    protected static ?string    $navigationGroup    = NavigationGroup::DEALER_GROUP->value;
    protected static ?int       $navigationSort     = NavigationSort::CAR_REGISTRATION->value;
    protected static ?string    $pluralModelLabel   = '車両登録';
    protected static ?string    $modelLabel         = '車両登録';
    
    // dealer/dealer_staffのみアクセス可能
    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['dealer', 'dealer_staff']);
    }

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
                        })
                        ->disabled(fn (Get $get) => !$get('series_id')),

                    Select::make('model_year')
                        ->label('年式')
                        ->options(function (Get $get) {
                            $versionId = $get('year_version_id');
                            if (!$versionId) return [];

                            $version = MstVehicleYearVersions::find($versionId);
                            if (!$version) return [];

                            return collect(range($version->year_from, $version->year_to))
                                ->mapWithKeys(fn ($year) => [$year => "{$year}年"])
                                ->toArray();
                        })
                        ->required()
                        ->live()
                        ->disabled(fn (Get $get) => !$get('year_version_id'))
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            // カタログスペック自動補完
                            $versionId = $get('year_version_id');
                            if (!$versionId)
                            {
                                return;
                            }
                            $version = MstVehicleYearVersions::find($versionId);
                            if (!$version)
                            {
                                return;
                            }
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
                        ])
                        ->columns(3),
                ])
                ->columns(2),

            // ===== ③車両詳細・スペック（stk_car_details） =====
            Section::make('車両スペック')
                ->schema([
                    TextInput::make('model_year')
                        ->label('年式')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(now()->year),

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
                        ->maxValue(6),

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

// CarRegistrationResource.php のフォームで
// ローンセクションを以下のように表示制御してください

// ===== ローン・諸費用設定セクションをディーラー権限で制御 =====

Section::make('ローン・諸費用設定')
    ->description(function () {
        $dealer = \App\Infrastructure\Eloquent\User\StkCarDealer::where('id', auth()->user()->dealer_id)->first();

        if (!$dealer) return 'ローン設定は利用できません';

        if ($dealer->loan_setting_enabled) {
            return 'ローンプランを設定できます（設定内容はディーラー責任となります）';
        }

        if ($dealer->isLoanSettingPending()) {
            return '⏳ ローン設定の申請中です。管理者の承認をお待ちください。';
        }

        return 'ローン設定を利用するには管理者への申請が必要です。';
    })
    ->collapsible()
    ->collapsed()
    ->schema(function () {
        $dealer = \App\Infrastructure\Eloquent\User\StkCarDealer::where('id', auth()->user()->dealer_id)->first();

        // 権限なしの場合は申請ボタンのみ表示
        if (!$dealer || !$dealer->loan_setting_enabled) {
            return [
                \Filament\Forms\Components\Placeholder::make('loan_request_info')
                    ->label('')
                    ->content(function () use ($dealer) {
                        if ($dealer?->isLoanSettingPending()) {
                            return '申請中です。管理者の承認後にローン設定が可能になります。';
                        }
                        if (!empty($dealer?->loan_setting_rejected_reason)) {
                            return "前回の申請は拒否されました。理由：{$dealer->loan_setting_rejected_reason}";
                        }
                        return 'ローン設定を利用するには申請が必要です。';
                    }),
            ];
        }

        // 権限ありの場合はRepeaterを表示
        return [
            \Filament\Forms\Components\Repeater::make('loans')
                ->label('ローンプラン')
                ->relationship('loans')
                ->columns(2)
                ->schema([
                    \Filament\Forms\Components\Select::make('loan_type')
                        ->label('ローンタイプ')
                        ->options(\App\Infrastructure\Eloquent\User\StkCarLoan::TYPE_LABELS)
                        ->default(\App\Infrastructure\Eloquent\User\StkCarLoan::TYPE_STANDARD)
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    \Filament\Forms\Components\TextInput::make('interest_rate')
                        ->label('金利（%）')
                        ->numeric()
                        ->step(0.1)
                        ->placeholder('例：3.9')
                        ->suffix('%'),

                    \Filament\Forms\Components\TextInput::make('loan_months')
                        ->label('ローン期間（月）')
                        ->numeric()
                        ->placeholder('例：60')
                        ->suffix('ヶ月'),

                    \Filament\Forms\Components\TextInput::make('down_payment')
                        ->label('頭金（円）')
                        ->numeric()
                        ->placeholder('例：300000'),

                    \Filament\Forms\Components\TextInput::make('misc_fee')
                        ->label('諸費用（円）')
                        ->numeric()
                        ->placeholder('未入力でシステム概算'),

                    \Filament\Forms\Components\TextInput::make('residual_value')
                        ->label('残価（円）')
                        ->numeric()
                        ->placeholder('残価設定ローンのみ')
                        ->visible(fn (\Filament\Forms\Get $get) => $get('loan_type') === 'residual'),

                    \Filament\Forms\Components\Textarea::make('note')
                        ->label('備考')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->addActionLabel('ローンプランを追加')
                ->maxItems(3),
        ];
    }),


// ===== ディーラー側のローン設定申請Action =====
// ListCarRegistrations.php または専用ページに以下のActionを追加

// \Filament\Actions\Action::make('request_loan_setting')
//     ->label('ローン設定を申請する')
//     ->color('warning')
//     ->icon('heroicon-o-banknotes')
//     ->visible(function () {
//         $dealer = \App\Infrastructure\Eloquent\User\StkCarDealer::where('id', auth()->user()->dealer_id)->first();
//         return $dealer && !$dealer->loan_setting_enabled && !$dealer->isLoanSettingPending();
//     })
//     ->form([
//         \Filament\Forms\Components\Textarea::make('reason')
//             ->label('申請理由')
//             ->required()
//             ->rows(4)
//             ->placeholder('ローン設定を希望する理由を入力してください'),
//     ])
//     ->modalHeading('ローン設定の申請')
//     ->modalDescription('管理者が内容を確認後に許可/拒否をお知らせします。')
//     ->modalSubmitActionLabel('申請する')
//     ->modalCancelActionLabel('キャンセル')
//     ->action(function (array $data) {
//         $dealer = \App\Infrastructure\Eloquent\User\StkCarDealer::where('id', auth()->user()->dealer_id)->first();
//         if (!$dealer) return;

//         $dealer->update([
//             'loan_setting_requested_by' => auth()->id(),
//             'loan_setting_reason'       => $data['reason'],
//             'loan_setting_requested_at' => now(),
//         ]);

//         \Filament\Notifications\Notification::make()
//             ->title('ローン設定の申請を送信しました。管理者の承認をお待ちください。')
//             ->success()
//             ->send();
//     }),
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
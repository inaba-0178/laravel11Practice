<?php
namespace App\Filament\Resources\TodayReservationResource\Pages;

use App\Filament\Resources\TodayReservationResource;
use App\Infrastructure\Eloquent\User\StkReservationResponse;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkReservation;
use Filament\Forms\Components\Placeholder;
use Filament\Actions\Action;

class CreateReservationResponse extends Page
{
    protected static string $resource = TodayReservationResource::class;
    protected static string $view     = 'filament.pages.create-reservation-response';

    public ?array $data = [];
    public $record;

    public function mount(int $record): void
    {
        $this->record = \App\Infrastructure\Eloquent\User\StkReservation::with([
            'schedule', 'member', 'dealer', 'response'
        ])->findOrFail($record);

        // 既存の対応内容がある場合はデータを引き継ぐ
        if ($this->record->response) {
            $this->data = $this->record->response->toArray();
        } else {
            // 予約情報から自動引継ぎ
            $this->data = [
                'handled_at'       => $this->record->schedule
                    ? $this->record->schedule->date . ' ' . $this->record->schedule->time_from
                    : now()->format('Y-m-d H:i:s'),
                'handled_by'       => Auth::id(),
                'customer_name'    => $this->record->member
                    ? $this->record->member->full_name
                    : ($this->record->guest_name ?? ''),
                'customer_phone'   => $this->record->member
                    ? $this->record->member->phone_number
                    : ($this->record->guest_phone ?? ''),
                'customer_address' => $this->record->member
                    ? $this->record->member->prefecture . $this->record->member->city . $this->record->member->address_line1
                    : ($this->record->guest_address ?? ''),
            ];
        }

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        $dealerStaff = User::where('dealer_id', Auth::user()->dealer_id)
            ->whereIn('role', ['dealer', 'dealer_staff'])
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();

        return $form
            ->schema([
                Section::make('対応情報')
                    ->schema([
                        DateTimePicker::make('handled_at')
                            ->label('対応日時')
                            ->required()
                            ->seconds(false),

                        Select::make('handled_by')
                            ->label('対応担当者')
                            ->options($dealerStaff)
                            ->required(),
                    ])->columns(2),

                Section::make('お客様情報')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('お客様名')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('customer_phone')
                            ->label('お客様連絡先')
                            ->required()
                            ->maxLength(20)
                            ->rules(['regex:/^[0-9\-]+$/'])
                            ->validationMessages([
                                'regex' => 'お客様連絡先に使用できない文字が含まれています',
                            ]),

                        TextInput::make('customer_address')
                            ->label('お客様住所')
                            ->required()
                            ->maxLength(255),
                    ])->columns(1),

                Section::make('来店内容')
                    ->schema([
                        Textarea::make('visit_purpose')
                            ->label('来店目的・内容')
                            ->required()
                            ->rows(3),

                        Textarea::make('response_content')
                            ->label('対応内容')
                            ->required()
                            ->rows(5),
                    ])->columns(1),

                Section::make('見積情報')
                    ->schema([       
                        Toggle::make('has_estimate')
                            ->label('見積あり')
                            ->live(),                 
                        Select::make('purchase_car_id')
                            ->label('見積車両')
                            ->options(function () {
                                $cars = StkCar::where('dealer_id', Auth::user()->dealer_id)->get();

                                return $cars->mapWithKeys(function ($car) {
                                    $series = MstCarSeries::with('mstManufacturer')->find($car->series_id);
                                    $seriesName      = $series ? $series->series_name : '不明';
                                    $manufacturerName = $series?->mstManufacturer?->display_name ?? '不明';
                                    return [
                                        $car->id => '在庫番号:' . ($car->stock_number ?? '-') . ' / ' . $manufacturerName . ' / ' . $seriesName . ' / ' . ($car->model_year ?? '-') . '年式'
                                    ];
                                })->toArray();
                            })
                            ->live()
                            ->visible(fn($get) => $get('has_estimate')),

                        Placeholder::make('car_price')
                            ->label('車両参考価格')
                            ->content(function ($get) {
                                $carId = $get('purchase_car_id');
                                if (!$carId) return '車両を選択してください';
                                $car = StkCar::find($carId);
                                if (!$car) return '---';
                                return '¥' . number_format($car->price);
                            })
                            ->visible(fn($get) => $get('has_estimate')),

                        TextInput::make('estimate_amount')
                            ->label('見積金額')
                            ->numeric()
                            ->prefix('¥')
                            ->visible(fn($get) => $get('has_estimate')),

                        TextInput::make('discount_amount')
                            ->label('値引き額')
                            ->numeric()
                            ->prefix('¥')
                            ->visible(fn($get) => $get('has_estimate')),

                        TextInput::make('miscellaneous_cost')
                            ->label('諸費用')
                            ->numeric()
                            ->prefix('¥')
                            ->visible(fn($get) => $get('has_estimate')),

                        Select::make('payment_method')
                            ->label('支払い方法')
                            ->options([
                                'cash'  => '現金',
                                'loan'  => 'ローン',
                                'other' => 'その他',
                            ])
                            ->live()
                            ->visible(fn($get) => $get('has_estimate')),

                        TextInput::make('loan_down_payment')
                            ->label('頭金')
                            ->numeric()
                            ->prefix('¥')
                            ->visible(fn($get) => $get('has_estimate') && $get('payment_method') === 'loan'),

                        TextInput::make('loan_monthly_amount')
                            ->label('月々支払い額')
                            ->numeric()
                            ->prefix('¥')
                            ->visible(fn($get) => $get('has_estimate') && $get('payment_method') === 'loan'),

                        TextInput::make('loan_count')
                            ->label('ローン回数')
                            ->numeric()
                            ->suffix('回')
                            ->visible(fn($get) => $get('has_estimate') && $get('payment_method') === 'loan'),

                    ])->columns(1),

                Section::make('購入情報')
                    ->schema([
                        Toggle::make('has_purchase')
                            ->label('購入あり')
                            ->live(),


                        DatePicker::make('contract_date')
                            ->label('契約日')
                            ->visible(fn($get) => $get('has_purchase')),

                        DatePicker::make('delivery_date')
                            ->label('納車予定日')
                            ->visible(fn($get) => $get('has_purchase')),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['reservation_id'] = $this->record->id;
        
        if ($this->record->response) {
            $this->record->response->update($data);
        } else {
            StkReservationResponse::create($data);
        }
        
        $this->dispatch('close-modal', id: 'confirm-save-modal');
        $this->dispatch('open-modal', id: 'success-modal');
    }

    public function getTitle(): string
    {
        return $this->record->response ? '対応内容編集' : '新規対応入力';
    }

    protected function rules(): array
    {
        return [
            'handled_at'        => ['required'],
            'handled_by'        => ['required'],
            'customer_name'     => ['required', 'string', 'max:100'],
            'customer_phone'    => ['required', 'string', 'max:20', 'regex:/^[0-9\-]+$/'],
            'customer_address'  => ['required', 'string', 'max:255'],
            'visit_purpose'     => ['required', 'string'],
            'response_content'  => ['required', 'string'],
            'has_estimate'      => ['required', 'boolean'],
        ];
    }


    protected function validationAttributes(): array
    {
        return [
            'handled_at'       => '対応日時',
            'handled_by'       => '対応担当者',
            'customer_name'    => 'お客様名',
            'customer_phone'   => 'お客様連絡先',
            'customer_address' => 'お客様住所',
            'visit_purpose'    => '来店目的・内容',
            'response_content' => '対応内容',
            'has_estimate'     => '見積有無',
        ];
    }

    protected function getValidationMessages(): array
    {
        return [
            'customer_phone.regex' => 'お客様連絡先に使用できない文字が含まれています',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('予約詳細へ戻る')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(TodayReservationResource::getUrl('view', ['record' => $this->record->id])),
        ];
    }

    public function validateAndOpenModal(): void
    {
        $data = $this->form->getState();
        
        $validator = \Validator::make($data, $this->rules(), $this->getValidationMessages(), $this->validationAttributes());
        
        if ($validator->fails()) {            
            // ★エラー処理追加
            foreach ($validator->errors()->messages() as $field => $messages) {
                $this->addError("data.$field", $messages[0]);
            }
            Notification::make()
                ->title('入力エラー')
                ->body('入力項目にエラーがあります')
                ->danger()
                ->send();
            return;
        }
        
        $this->dispatch('open-modal', id: 'confirm-save-modal');
    }



}
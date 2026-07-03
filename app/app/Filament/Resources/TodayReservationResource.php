<?php
namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Constants\ReservationStatus;
use App\Filament\Resources\TodayReservationResource\Pages;
use App\Infrastructure\Eloquent\User\StkReservation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\Grid;

class TodayReservationResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = StkReservation::class;
    protected static ?string $navigationIcon   = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::DEALER_RESERVATION_TODAY->value;
    protected static ?string $pluralModelLabel = '本日の予約';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        $dealerId = $user->getEffectiveDealerId();
        if ($dealerId) {
            $query->where('stk_reservations.dealer_id', $dealerId);
        }

        return $query
            ->with(['schedule', 'car', 'member', 'dealer'])
            ->whereHas('schedule', function (Builder $q) {
                $q->where('date', now()->toDateString());
            })
            ->where('stk_reservations.status', ReservationStatus::CONFIRMED->value)
            ->select('stk_reservations.*');
    }
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('schedule.time_from')
                    ->label('開始時間')
                    ->time('H:i'),

                TextColumn::make('schedule.time_to')
                    ->label('終了時間')
                    ->time('H:i'),

                TextColumn::make('status')
                    ->label('ステータス')
                    ->badge()
                    ->color(fn(string $state): string => ReservationStatus::colorFromValue($state))
                    ->formatStateUsing(fn(string $state): string => ReservationStatus::labelFromValue($state)),

                TextColumn::make('guest_name')
                    ->label('お名前')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->member) {
                            return $record->member->full_name;
                        }
                        return $state ?? '---';
                    }),

                TextColumn::make('guest_phone')
                    ->label('電話番号')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->member) {
                            return $record->member->phone_number;
                        }
                        return $state ?? '---';
                    }),

                TextColumn::make('guest_email')
                    ->label('メールアドレス')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->member) {
                            return $record->member->email;
                        }
                        return $state ?? '---';
                    }),

                TextColumn::make('car.stock_number')
                    ->label('在庫番号'),
            ])
            ->filters([
                //TODO なんか使いづらいので後で修正するかも
                Filter::make('time_from')
                    ->label('開始時間')
                    ->form([
                        Grid::make(1)  // ★1列に変更
                            ->schema([
                                Grid::make(2)  // ★中身を2列Gridで横並び
                                    ->schema([
                                        TimePicker::make('time_from_start')
                                            ->label('開始時間（から）')
                                            ->seconds(false)
                                            ->minutesStep(10),
                                        TimePicker::make('time_from_end')
                                            ->label('開始時間（まで）')
                                            ->seconds(false)
                                            ->minutesStep(10)
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['time_from_start'])) {
                            $query->whereHas('schedule', function (Builder $q) use ($data) {
                                $q->where('time_from', '>=', $data['time_from_start']);
                            });
                        }
                        if (!empty($data['time_from_end'])) {
                            $query->whereHas('schedule', function (Builder $q) use ($data) {
                                $q->where('time_from', '<=', $data['time_from_end']);
                            });
                        }
                    }),

                Filter::make('name')
                    ->label('お名前')
                    ->form([
                        TextInput::make('name')
                            ->label('お名前')
                            ->placeholder('部分一致で検索'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['name'])) {
                            $query->where(function (Builder $q) use ($data) {
                                $q->where('guest_name', 'LIKE', '%' . $data['name'] . '%')
                                  ->orWhereHas('member', function (Builder $mq) use ($data) {
                                      $mq->where('sei', 'LIKE', '%' . $data['name'] . '%')
                                         ->orWhere('mei', 'LIKE', '%' . $data['name'] . '%')
                                         ->orWhere('sei_kana', 'LIKE', '%' . $data['name'] . '%')
                                         ->orWhere('mei_kana', 'LIKE', '%' . $data['name'] . '%');
                                  });
                            });
                        }
                    }),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->actions([
                ViewAction::make()->label('詳細'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => Pages\ListTodayReservations::route('/'),
            'view'     => Pages\ViewTodayReservation::route('/{record}'),
            'response' => Pages\CreateReservationResponse::route('/{record}/response'),
        ];
    }
}
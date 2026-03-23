<?php
namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Resources\ReservationResource\Pages;
use App\Infrastructure\Eloquent\User\StkReservation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReservationResource extends Resource
{
    protected static ?string $model             = StkReservation::class;
    protected static ?string $navigationIcon    = 'heroicon-o-calendar';
    protected static ?string $navigationGroup   = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort    = NavigationSort::DEALER_RESERVATION_LIST->value;
    protected static ?string $pluralModelLabel  = '予約一覧';

    // ログインユーザーのディーラーに絞る
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        if ($user->isDealerRole()) {
            $query->where('stk_reservations.dealer_id', $user->dealer_id);
        }

        return $query->with(['schedule', 'car', 'member']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('schedule.date')
                    ->label('予約日')
                    ->date('Y/m/d')
                    ->sortable(),

                TextColumn::make('schedule.time_from')
                    ->label('開始時間')
                    ->time('H:i'),

                TextColumn::make('schedule.time_to')
                    ->label('終了時間')
                    ->time('H:i'),

                TextColumn::make('status')
                    ->label('ステータス')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'no_show'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending'   => '仮予約',
                        'confirmed' => '承認済み',
                        'completed' => '対応完了',
                        'no_show'   => '未来店',
                        'cancelled' => 'キャンセル',
                        default     => $state,
                    }),

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
                SelectFilter::make('status')
                    ->label('ステータス')
                    ->options([
                        'pending'   => '仮予約',
                        'confirmed' => '承認済み',
                        'completed' => '対応完了',
                        'no_show'   => '未来店',
                        'cancelled' => 'キャンセル',
                    ]),
            ])
            ->actions([
                ViewAction::make()->label('詳細'),
            ])
            // 予約日が3日以内の行を赤くする
            ->recordClasses(fn(StkReservation $record): string => 'bg-danger-100')
            ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'view'  => Pages\ViewReservation::route('/{record}'),
        ];
    }
}
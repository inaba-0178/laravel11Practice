<?php
namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Constants\ReservationStatus;
use App\Infrastructure\Eloquent\User\StkReservation;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    public function getTitle(): string
    {
        return '予約詳細';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    // 同一ユーザーの他のpending予約を取得
    private function getOtherPendingReservations()
    {
        $query = StkReservation::with('schedule')
            ->where('dealer_id', $this->record->dealer_id)
            ->where('status', 'pending')
            ->where('id', '!=', $this->record->id);

        if ($this->record->member_id) {
            $query->where('member_id', $this->record->member_id);
        } else {
            $query->where(function ($q) {
                if ($this->record->guest_email) {
                    $q->orWhere('guest_email', $this->record->guest_email);
                }
                if ($this->record->guest_phone) {
                    $q->orWhere('guest_phone', $this->record->guest_phone);
                }
            });
        }

        return $query->get();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('予約情報')
                ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold;'])
                    ->schema([
                        TextEntry::make('schedule.date')
                            ->label('予約日')
                            ->date('Y/m/d'),

                        TextEntry::make('schedule_time')
                            ->label('予約時間')
                            ->getStateUsing(fn($record) =>
                                $record->schedule
                                    ? $record->schedule->time_from . ' ~ ' . $record->schedule->time_to
                                    : '---'
                            ),

                        TextEntry::make('reservationType.name')
                            ->label('予約種別'),

                        TextEntry::make('status')
                            ->label('ステータス')
                            ->badge()
                            ->color(
                                fn(string $state): string =>
                                    ReservationStatus::colorFromValue($state)
                            )
                            ->formatStateUsing(
                                fn(string $state): string => 
                                    ReservationStatus::labelFromValue($state)
                            ),
                    ])->columns(1),

                Section::make('お客様情報')
                    ->schema([
                        TextEntry::make('name')
                            ->label('お名前')
                            ->getStateUsing(function ($record) {
                                if ($record->member) {
                                    return $record->member->full_name;
                                }
                                return $record->guest_name ?? '---';
                            }),

                        TextEntry::make('phone')
                            ->label('電話番号')
                            ->getStateUsing(function ($record) {
                                if ($record->member) {
                                    return $record->member->phone_number;
                                }
                                return $record->guest_phone ?? '---';
                            }),

                        TextEntry::make('email')
                            ->label('メールアドレス')
                            ->getStateUsing(function ($record) {
                                if ($record->member) {
                                    return $record->member->email;
                                }
                                return $record->guest_email ?? '---';
                            }),

                        TextEntry::make('address')
                            ->label('住所')
                            ->getStateUsing(function ($record) {
                                if ($record->member) {
                                    return $record->member->prefecture
                                        . $record->member->city
                                        . $record->member->address_line1;
                                }
                                return $record->guest_address ?? '---';
                            }),
                    ])->columns(1),

                Section::make('車両情報')
                    ->schema([
                        TextEntry::make('car_name')
                            ->label('車両名')
                            ->getStateUsing(function ($record) {
                                if (!$record->car) return '---';
                                $series = \App\Infrastructure\Eloquent\Mst\MstCarSeries::find($record->car->series_id);
                                return $series ? $series->series_name : '---';
                            }),

                        TextEntry::make('car.stock_number')
                            ->label('在庫番号'),

                        TextEntry::make('car.price')
                            ->label('価格')
                            ->money('JPY'),

                        TextEntry::make('car.model_year')
                            ->label('年式'),

                        TextEntry::make('car_url')
                            ->label('車両ページ')
                            ->getStateUsing(fn($record) =>
                                $record->car
                                    ? config('app.frontend_url') . '/cars/' . $record->car->id
                                    : '---'
                            )
                            ->url(fn($record) =>
                                $record->car
                                    ? config('app.frontend_url') . '/cars/' . $record->car->id
                                    : null
                            )
                            ->openUrlInNewTab(),
                    ])->columns(1),

                Section::make('備考')
                    ->schema([
                        TextEntry::make('memo')
                            ->label('備考'),
                    ])->columns(1),



                InfolistActions::make([
                    InfolistAction::make('reject_bottom')
                        ->label('否認')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->size('lg')
                        ->extraAttributes(['style' => 'padding: 1rem 3rem; font-size: 1.3rem;'])
                        ->visible(fn($record) => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->modalHeading('予約を否認しますか？')
                        ->modalDescription('この予約を否認します。よろしいですか？')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->modalSubmitActionLabel('否認する')
                            ->action(function ($record) {
                                $record->update([
                                    'status'     => 'cancelled',
                                    'handled_by' => Auth::id(),
                                    'handled_at' => now(),
                                ]);
                                    // TODO: 否認メール送信
                                    Notification::make()->title('予約を否認しました')->success()->send();
                                })
                        ->modalWidth('lg')
                        ->visible(fn($record) => 
                            $record->status === 'pending' &&
                            $record->schedule &&
                            \Carbon\Carbon::parse($record->schedule->date . ' ' . $record->schedule->time_from)->isFuture()
                        ),

                    InfolistAction::make('approve_bottom')
                        ->label('承認')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->size('lg')
                        ->extraAttributes(['style' => 'padding: 1rem 3rem; font-size: 1.3rem;'])
                        ->visible(fn($record) => $record->status === 'pending')
                        ->form(function () {
                            $otherReservations = $this->getOtherPendingReservations();
                            if ($otherReservations->isEmpty()) return [];

                            $options = [
                                $this->record->id => $this->record->schedule->date . ' ' . $this->record->schedule->time_from . ' ~ ' . $this->record->schedule->time_to . '（この予約）',
                                    ...$otherReservations->mapWithKeys(fn($r) => [
                                     $r->id => $r->schedule->date . ' ' . $r->schedule->time_from . ' ~ ' . $r->schedule->time_to
                                ])->toArray(),
                            ];

                            return [
                                Radio::make('selected_reservation_id')
                                    ->label('対応する予約日時を選択してください')
                                    ->options($options)
                                    ->default($this->record->id)
                                    ->required(),
                            ];
                        })
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-check-circle')
                        ->modalHeading('予約を承認しますか？')
                        ->modalDescription('この予約を承認します。よろしいですか？')
                        ->modalSubmitActionLabel('承認する')
                        ->action(function ($record, array $data) {
                            $otherReservations = $this->getOtherPendingReservations();
                            $selectedId        = (int)($data['selected_reservation_id'] ?? $record->id);
                            $allReservations   = $otherReservations->push($record);

                            foreach ($allReservations as $reservation) {
                                if ($reservation->id === $selectedId) {
                                    $reservation->update([
                                        'status'     => 'confirmed',
                                        'handled_by' => Auth::id(),
                                        'handled_at' => now(),
                                    ]);
                                    // TODO: 承認メール送信
                                } else {
                                    $reservation->update([
                                        'status'     => 'cancelled',
                                        'handled_by' => Auth::id(),
                                        'handled_at' => now(),
                                    ]);
                                    // TODO: キャンセルメール送信
                                }
                            }

                            Notification::make()->title('予約を承認しました')->success()->send();
                        })
                        ->modalWidth('lg')
                        ->visible(fn($record) => 
                            $record->status === 'pending' &&
                            $record->schedule &&
                            \Carbon\Carbon::parse($record->schedule->date . ' ' . $record->schedule->time_from)->isFuture()
                        ),

                    InfolistAction::make('cancel_bottom')
                        ->label('キャンセル')
                        ->color('warning')
                        ->icon('heroicon-o-x-mark')
                        ->size('lg')
                        ->requiresConfirmation()
                        ->extraAttributes(['style' => 'padding: 1rem 3rem; font-size: 1.3rem;'])
                        ->visible(fn($record) => $record->status === 'confirmed')
                        ->modalHeading('予約をキャンセルしますか？')
                        ->modalDescription('この予約をキャンセルします。よろしいですか？')
                        ->modalSubmitActionLabel('予約キャンセル')
                        ->action(function ($record) {
                            $record->update([
                                'status'     => 'cancelled',
                                'handled_by' => Auth::id(),
                                'handled_at' => now(),
                            ]);
                            // TODO: キャンセルメール送信
                            Notification::make()->title('予約をキャンセルしました')->success()->send();
                        })
                        ->modalWidth('lg')
                        ->visible(fn($record) => 
                            $record->status === 'confirmed' &&
                            $record->schedule &&
                            \Carbon\Carbon::parse($record->schedule->date . ' ' . $record->schedule->time_from)->isFuture()
                        ),
                ]),
                        
            ]);
    }
}
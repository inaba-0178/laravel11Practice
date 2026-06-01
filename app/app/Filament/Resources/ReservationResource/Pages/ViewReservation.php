<?php
namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Constants\ReservationStatus;
use App\Infrastructure\Eloquent\User\StkReservation;
use Filament\Forms\Components\Radio;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Application\Services\MailService;
use App\Domain\Shared\Constants\MailTemplateKey;
use Carbon\Carbon;

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
                                $series = MstCarSeries::find($record->car->series_id);
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
                                    'status'     => ReservationStatus::DENIAL->value,
                                    'handled_by' => Auth::id(),
                                    'handled_at' => now(),
                                ]);
                                    $email = $this->getEmailAddress($record);
                                    if ($email) {
                                        app(MailService::class)->send(
                                            templateKey:  MailTemplateKey::RESERVATION_DENIAL,
                                            toEmail:      $email,
                                            placeholders: $this->getPlaceholders($record),
                                        );
                                    }
                                    Notification::make()->title('予約を否認しました')->success()->send();
                                })
                        ->modalWidth('lg')
                        ->visible(fn($record) => 
                            $record->status === 'pending' &&
                            $record->schedule &&
                            Carbon::parse($record->schedule->date->format('Y-m-d') . ' ' . $record->schedule->time_from)->isFuture()
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
                                $this->record->id => $this->record->schedule->date->format('Y-m-d') . ' ' . $this->record->schedule->time_from . ' ~ ' . $this->record->schedule->time_to . '（この予約）',
                                ...$otherReservations->mapWithKeys(fn($r) => [
                                    $r->id => $r->schedule->date->format('Y-m-d') . ' ' . $r->schedule->time_from . ' ~ ' . $r->schedule->time_to
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
                                        'status'     => ReservationStatus::CONFIRMED->value,
                                        'handled_by' => Auth::id(),
                                        'handled_at' => now(),
                                    ]);
                                    // TODO: 承認メール送信
                                    $email = $this->getEmailAddress($reservation);
                                    if ($email) {
                                        app(\App\Application\Services\MailService::class)->send(
                                            templateKey:  \App\Domain\Shared\Constants\MailTemplateKey::RESERVATION_CONFIRMED,
                                            toEmail:      $email,
                                            placeholders: $this->getPlaceholders($reservation),
                                        );
                                    }
                                } else {
                                    $reservation->update([
                                        'status'     => ReservationStatus::CANCELLED_BY_SYSTEM->value,
                                        'handled_by' => Auth::id(),
                                        'handled_at' => now(),
                                    ]);
                                    // TODO: キャンセルメール送信
                                    $email = $this->getEmailAddress($reservation);
                                    if ($email) {
                                        app(\App\Application\Services\MailService::class)->send(
                                            templateKey:  \App\Domain\Shared\Constants\MailTemplateKey::RESERVATION_CANCELLED_CUSTOMER,
                                            toEmail:      $email,
                                            placeholders: $this->getPlaceholders($reservation),
                                        );
                                    }
                                }
                            }

                            Notification::make()->title('予約を承認しました')->success()->send();
                        })
                        ->modalWidth('lg')
                        ->visible(fn($record) => 
                            $record->status === 'pending' &&
                            $record->schedule &&
                            \Carbon\Carbon::parse($record->schedule->date->format('Y-m-d') . ' ' . $record->schedule->time_from)->isFuture()
                        ),

                    InfolistAction::make('dealer_trouble')
                        ->label('ディーラー都合キャンセル')
                        ->color('warning')
                        ->icon('heroicon-o-x-mark')
                        ->size('lg')
                        ->visible(fn($record) => $record->status === 'confirmed')
                        ->modalHeading('ディーラー都合でキャンセルしますか？')
                        ->modalDescription('お客様にディーラー都合のキャンセルメールが送信されます。よろしいですか？')
                        ->modalSubmitActionLabel('ディーラー都合でキャンセル')
                        ->action(function ($record) {
                            $record->update([
                                'status'     => ReservationStatus::CANCELLED_DEALER_TROUBLE->value,
                                'handled_by' => Auth::id(),
                                'handled_at' => now(),
                            ]);
                            // TODO: キャンセルメール送信
                            $email = $this->getEmailAddress($record);
                            if ($email) {
                                app(\App\Application\Services\MailService::class)->send(
                                    templateKey:  \App\Domain\Shared\Constants\MailTemplateKey::RESERVATION_CANCELLED_DEALER_TROUBLE,
                                    toEmail:      $email,
                                    placeholders: $this->getPlaceholders($record),
                                );
                            }
                            Notification::make()->title('予約をキャンセルしました')->success()->send();
                        })
                        ->modalWidth('lg')
                        ->visible(fn($record) => 
                            $record->status === 'confirmed' &&
                            $record->schedule &&
                            \Carbon\Carbon::parse($record->schedule->date->format('Y-m-d') . ' ' . $record->schedule->time_from)->isFuture()
                        ),
                    InfolistAction::make('dealer_car_sold')
                        ->label('車両成約によるキャンセル')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->size('lg')
                        ->visible(fn($record) =>
                            $record->status === 'confirmed' &&
                            $record->schedule &&
                            \Carbon\Carbon::parse($record->schedule->date->format('Y-m-d') . ' ' . $record->schedule->time_from)->isFuture()
                        )
                        ->requiresConfirmation()
                        ->modalHeading('問い合わせいただいた車両がすでにご成約が決まったためキャンセルしますか？')
                        ->modalDescription('お客様に問い合わせ車両成約のキャンセルメールが送信されます。よろしいですか？')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->modalSubmitActionLabel('キャンセルする')
                        ->action(function ($record) {
                            $record->update([
                                'status'      => ReservationStatus::CANCELLED_DEALER_CAR_SOLD->value,
                                'cancel_type' => 'dealer',
                                'handled_by'  => Auth::id(),
                                'handled_at'  => now(),
                            ]);
                            // TODO: 車両成約キャンセルメール送信
                            $email = $this->getEmailAddress($record);
                            if ($email) {
                                app(\App\Application\Services\MailService::class)->send(
                                    templateKey:  \App\Domain\Shared\Constants\MailTemplateKey::RESERVATION_CANCELLED_DEALER,
                                    toEmail:      $email,
                                    placeholders: $this->getPlaceholders($record),
                                );
                            }
                            Notification::make()->title('キャンセルしました')->success()->send();
                        }),

                    // お客様都合キャンセル（confirmedのみ）
                    InfolistAction::make('customer')
                        ->label('お客様都合でキャンセル')
                        ->color('warning')
                        ->icon('heroicon-o-x-mark')
                        ->size('lg')
                        ->visible(fn($record) =>
                            $record->status === 'confirmed' &&
                            $record->schedule &&
                            \Carbon\Carbon::parse($record->schedule->date->format('Y-m-d') . ' ' . $record->schedule->time_from)->isFuture()
                        )
                        ->requiresConfirmation()
                        ->modalHeading('お客様都合でキャンセルしますか？')
                        ->modalDescription('お客様にキャンセル受付メールが送信されます。よろしいですか？')
                        ->modalSubmitActionLabel('キャンセルする')
                        ->action(function ($record) {
                            $record->update([
                                'status'      => ReservationStatus::CANCELLED_CUSTOMER->value,
                                'cancel_type' => 'customer',
                                'handled_by'  => Auth::id(),
                                'handled_at'  => now(),
                            ]);
                            // TODO: お客様都合キャンセルメール送信
                            $email = $this->getEmailAddress($record);
                            if ($email) {
                                app(\App\Application\Services\MailService::class)->send(
                                    templateKey:  \App\Domain\Shared\Constants\MailTemplateKey::RESERVATION_CANCELLED_CUSTOMER,
                                    toEmail:      $email,
                                    placeholders: $this->getPlaceholders($record),
                                );
                            }
                            Notification::make()->title('キャンセルしました')->success()->send();
                        }),
                ]),
                        
            ]);
    }

    private function getEmailAddress($record): ?string
    {
        if ($record->member) {
            return $record->member->email;
        }
        return $record->guest_email ?? null;
    }

    private function getPlaceholders($record): array
    {
        $dealer      = $record->dealer;
        $handledUser = $record->handled_by
            ? \App\Models\User::find($record->handled_by)
            : null;

        return [
            'guest_name'       => $record->member
                ? $record->member->full_name
                : ($record->guest_name ?? 'お客様'),
            'dealer_name'      => $dealer?->name ?? '',
            'staff_name'       => $handledUser?->name ?? '',
            'reservation_date' => $record->schedule
                ? $record->schedule->date->format('Y-m-d') . ' ' . $record->schedule->time_from . ' ～ ' . $record->schedule->time_to
                : '',
            'reservation_type' => $record->reservationType?->name ?? '',
            'car_name'         => $record->car
                ? (\App\Infrastructure\Eloquent\Mst\MstCarSeries::find($record->car->series_id)?->series_name ?? '')
                : '',
            'dealer_address'   => ($dealer?->city ?? '') . ($dealer?->address_detail ?? ''),
            'dealer_phone'     => $dealer?->phone ?? '',
            'dealer_email'     => $dealer?->email ?? '',
            'dealer_hours'     => $dealer?->business_hours ?? '',
        ];
    }
}
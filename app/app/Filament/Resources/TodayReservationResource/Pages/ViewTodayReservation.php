<?php
namespace App\Filament\Resources\TodayReservationResource\Pages;

use App\Constants\ReservationStatus;
use App\Filament\Resources\TodayReservationResource;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\User\StkReservation;
use App\Infrastructure\Eloquent\User\StkGuestCautionList;
use Filament\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewTodayReservation extends ViewRecord
{
    protected static string $resource = TodayReservationResource::class;

    public function getTitle(): string
    {
        return '本日の予約詳細';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('予約情報')
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
                            ->color(fn(string $state): string => ReservationStatus::colorFromValue($state))
                            ->formatStateUsing(fn(string $state): string => ReservationStatus::labelFromValue($state)),
                    ])->columns(1),

                Section::make('お客様情報')
                    ->schema([
                        TextEntry::make('name')
                            ->label('お名前')
                            ->getStateUsing(function ($record) {
                                if ($record->member) return $record->member->full_name;
                                return $record->guest_name ?? '---';
                            }),

                        TextEntry::make('phone')
                            ->label('電話番号')
                            ->getStateUsing(function ($record) {
                                if ($record->member) return $record->member->phone_number;
                                return $record->guest_phone ?? '---';
                            }),

                        TextEntry::make('email')
                            ->label('メールアドレス')
                            ->getStateUsing(function ($record) {
                                if ($record->member) return $record->member->email;
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

                // 対応内容セクション（入力済みの場合のみ表示）
                Section::make('対応内容')
                    ->schema([
                        TextEntry::make('response.visit_purpose')
                            ->label('来店目的・内容'),

                        TextEntry::make('response.response_content')
                            ->label('対応内容'),

                        TextEntry::make('response.has_estimate')
                            ->label('見積有無')
                            ->formatStateUsing(fn($state) => $state ? 'あり' : 'なし'),

                        TextEntry::make('response.created_at')
                            ->label('初登録日時')
                            ->dateTime('Y/m/d H:i'),

                        TextEntry::make('response.updated_at')
                            ->label('最終更新日時')
                            ->dateTime('Y/m/d H:i'),
                    ])
                    ->columns(1)
                    ->visible(fn($record) => $record->response !== null),

                InfolistActions::make([
                    // 新規対応入力 or 対応内容編集ボタン
                    InfolistAction::make('response_input')
                        ->label(fn($record) => $record->response ? '対応内容編集' : '新規対応入力')
                        ->color('primary')
                        ->icon('heroicon-o-pencil-square')
                        ->size('lg')
                        ->url(fn($record) => TodayReservationResource::getUrl('response', ['record' => $record->id])),

                    // 対応完了ボタン
                    InfolistAction::make('complete')
                        ->label('対応完了')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->size('lg')
                        ->visible(fn($record) =>
                            $record->status === ReservationStatus::CONFIRMED->value
                        )
                        ->requiresConfirmation()
                        ->modalHeading('対応完了にしますか？')
                        ->modalDescription('この予約を対応完了にします。よろしいですか？')
                        ->modalSubmitActionLabel('完了にする')
                        ->action(function ($record) {
                            $record->update([
                                'status'     => ReservationStatus::COMPLETED->value,
                                'handled_by' => Auth::id(),
                                'handled_at' => now(),
                            ]);
                            Notification::make()->title('対応完了にしました')->success()->send();
                        }),

                    // 未来店ボタン
                    InfolistAction::make('no_show')
                        ->label('未来店')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->size('lg')
                        ->visible(fn($record) =>
                            $record->status === ReservationStatus::CONFIRMED->value
                        )
                        ->requiresConfirmation()
                        ->modalHeading('未来店にしますか？')
                        ->modalDescription('この予約を未来店にします。よろしいですか？')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->modalSubmitActionLabel('未来店にする')
                        ->action(function ($record) {
                            $record->update([
                                'status'     => ReservationStatus::NO_SHOW->value,
                                'handled_by' => Auth::id(),
                                'handled_at' => now(),
                            ]);
                            Notification::make()->title('未来店にしました')->success()->send();
                        }),

                    // 要注意人物登録ボタン
                    InfolistAction::make('caution')
                        ->label('要注意人物登録')
                        ->color('warning')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->size('lg')
                        ->requiresConfirmation()
                        ->modalHeading('要注意人物登録しますか？')
                        ->modalDescription(<<<EOD
毎回予約はされるが突然キャンセルされる\n
または連絡もなく来店されない\n
来店された際にトラブルがあったなど\n
トラブルが多いお客様の場合要注意人物として登録することができます。\n
このお客様を要注意リストに登録しますがよろしいですか？
EOD)
                        ->modalSubmitActionLabel('登録する')
                        ->action(function ($record) {
                            // 会員の場合
                            if ($record->member_id) {
                                \App\Infrastructure\Eloquent\User\UsrUser::where('id', $record->member_id)
                                    ->update(['status' => 'caution']);
                            } else {
                                // ゲストの場合
                                \App\Infrastructure\Eloquent\User\StkGuestCautionList::create([
                                    'email'         => $record->guest_email,
                                    'phone'         => $record->guest_phone,
                                    'level'         => 'caution',
                                    'registered_by' => Auth::id(),
                                ]);
                            }
                            Notification::make()->title('要注意登録しました')->warning()->send();
                        }),
                ]),
            ]);
    }
}
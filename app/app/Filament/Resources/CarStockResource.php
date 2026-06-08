<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\CarStatus;
use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Resources\CarStockResource\Pages\ListCarStocks;
use App\Filament\Resources\CarStockResource\Pages\ViewCarStock;
use App\Infrastructure\Eloquent\User\StkCar;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CarStockResource extends Resource
{
    protected static ?string $model            = StkCar::class;
    protected static ?string $navigationIcon   = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::CAR_STOCK->value;
    protected static ?string $pluralModelLabel = '車両一覧';
    protected static ?string $modelLabel       = '車両一覧';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['dealer', 'dealer_staff']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                StkCar::query()
                    ->where('dealer_id', Auth::user()?->dealer_id)
                    ->whereIn('status', [
                        CarStatus::APPROVED_PENDING,
                        CarStatus::SCHEDULED,
                        CarStatus::AVAILABLE,
                        CarStatus::RESERVED,
                        CarStatus::PUBLISH_ENDED,
                        CarStatus::SOLD,
                    ])
            )
            ->columns([
                Tables\Columns\ImageColumn::make('main_image_url')
                    ->label('画像')
                    ->disk('s3')
                    ->height(60)
                    ->width(80),

                Tables\Columns\TextColumn::make('series.series_name')
                    ->label('車体名'),

                Tables\Columns\TextColumn::make('model_year')
                    ->label('年式')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}年" : '-'),

                Tables\Columns\TextColumn::make('price')
                    ->label('価格')
                    ->money('JPY'),

                Tables\Columns\TextColumn::make('mileage')
                    ->label('走行距離')
                    ->formatStateUsing(fn ($state) => number_format($state) . 'km'),

                Tables\Columns\TextColumn::make('status')
                    ->label('ステータス')
                    ->badge()
                    ->color(fn (string $state) => CarStatus::COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state) => CarStatus::LABELS[$state] ?? $state),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('公開日時')
                    ->dateTime('Y/m/d H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('登録日時')
                    ->dateTime('Y/m/d H:i'),
            ])
            ->actions([
                // ===== 今すぐ公開（approved_pending / scheduled） =====
                Tables\Actions\Action::make('publish_now')
                    ->label('今すぐ公開')
                    ->color('success')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (StkCar $record) => in_array($record->status, [
                        CarStatus::APPROVED_PENDING,
                        CarStatus::SCHEDULED,
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('今すぐ公開しますか？')
                    ->modalSubmitActionLabel('公開する')
                    ->modalCancelActionLabel('キャンセル')
                    ->action(fn (StkCar $record) => $record->update([
                        'status'       => CarStatus::AVAILABLE,
                        'published_at' => now(),
                    ])),

                // ===== 公開日時を指定（approved_pending） =====
                Tables\Actions\Action::make('schedule_publish')
                    ->label('公開日時を指定')
                    ->color('info')
                    ->icon('heroicon-o-clock')
                    ->visible(fn (StkCar $record) => in_array($record->status, [
                        CarStatus::APPROVED_PENDING,
                        CarStatus::SCHEDULED,
                    ]))
                    ->form([
                        \Filament\Forms\Components\DateTimePicker::make('publish_at')
                            ->label('公開日時')
                            ->required()
                            ->minDate(now())
                            ->default(fn (StkCar $record) => $record->status === CarStatus::SCHEDULED
                                ? $record->published_at
                                : now()->addHour()
                            ),
                    ])
                    ->action(fn (StkCar $record, array $data) => $record->update([
                        'status'       => CarStatus::SCHEDULED,
                        'published_at' => $data['publish_at'],
                    ])),

                // ===== 公開取消（scheduled） =====
                Tables\Actions\Action::make('cancel_schedule')
                    ->label('公開取消')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (StkCar $record) => $record->status === CarStatus::SCHEDULED)
                    ->requiresConfirmation()
                    ->modalHeading('公開日時指定を取り消しますか？')
                    ->modalSubmitActionLabel('取り消す')
                    ->modalCancelActionLabel('キャンセル')
                    ->action(fn (StkCar $record) => $record->update([
                        'status'       => CarStatus::APPROVED_PENDING,
                        'published_at' => null,
                    ])),

                // ===== 公開停止（available） =====
                Tables\Actions\Action::make('unpublish')
                    ->label('公開停止')
                    ->color('warning')
                    ->icon('heroicon-o-eye-slash')
                    ->visible(fn (StkCar $record) => $record->status === CarStatus::AVAILABLE)
                    ->requiresConfirmation()
                    ->modalHeading('公開を停止しますか？')
                    ->modalDescription('停止すると承認済み公開前に戻ります。')
                    ->modalSubmitActionLabel('停止する')
                    ->modalCancelActionLabel('キャンセル')
                    ->action(fn (StkCar $record) => $record->update([
                        'status'       => CarStatus::APPROVED_PENDING,
                        'published_at' => null,
                    ])),

                // ===== 販売終了（available） =====
                Tables\Actions\Action::make('mark_sold')
                    ->label('販売終了')
                    ->color('danger')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (StkCar $record) => $record->status === CarStatus::AVAILABLE)
                    ->requiresConfirmation()
                    ->modalHeading('販売終了にしますか？')
                    ->modalDescription('販売終了にすると公開が停止されます。')
                    ->modalSubmitActionLabel('販売終了にする')
                    ->modalCancelActionLabel('キャンセル')
                    ->action(fn (StkCar $record) => $record->update([
                        'status'  => CarStatus::SOLD,
                        'sold_at' => now(),
                    ])),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (StkCar $record) => ViewCarStock::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarStocks::route('/'),
            'view'  => ViewCarStock::route('/{record}'),
        ];
    }
}
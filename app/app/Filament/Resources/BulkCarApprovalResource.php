<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Resources\BulkCarApprovalResource\Pages\ListBulkCarApprovals;
use App\Filament\Resources\BulkCarApprovalResource\Pages\ViewBulkCarApproval;
use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables;
use Filament\Tables\Table;

class BulkCarApprovalResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = StkBulkUploadBatch::class;
    protected static ?string $navigationIcon   = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup  = NavigationGroup::ADMIN_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::BULK_CAR_APPROVAL->value;
    protected static ?string $pluralModelLabel = '一括車両承認';
    protected static ?string $modelLabel       = '一括車両承認';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super', 'admin']);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = StkBulkUploadBatch::whereHas('cars', fn ($q) => $q->where('status', 'pending'))->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('バッチID')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "#{$state}"),

                Tables\Columns\TextColumn::make('dealer.name')
                    ->label('ディーラー'),

                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('登録者'),

                Tables\Columns\TextColumn::make('total_count')
                    ->label('合計')
                    ->formatStateUsing(fn ($state) => "{$state}台"),

                Tables\Columns\TextColumn::make('pending_count')
                    ->label('承認待ち')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => "{$state}台"),

                Tables\Columns\TextColumn::make('approved_count')
                    ->label('承認済み')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => "{$state}台"),

                Tables\Columns\TextColumn::make('rejected_count')
                    ->label('差し戻し')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => "{$state}台"),

                Tables\Columns\TextColumn::make('status_label')
                    ->label('ステータス')
                    ->badge()
                    ->color(fn (StkBulkUploadBatch $record) => match($record->status) {
                        'published'        => 'success',
                        'approved_pending' => 'info',
                        'rejected'         => 'danger',
                        'partial_rejected' => 'warning',
                        default            => 'gray',
                    }),

                Tables\Columns\TextColumn::make('uploaded_at')
                    ->label('アップロード日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('uploaded_at', 'desc')
            ->recordUrl(fn (StkBulkUploadBatch $record) => ViewBulkCarApproval::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBulkCarApprovals::route('/'),
            'view'  => ViewBulkCarApproval::route('/{record}'),
        ];
    }

}
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DealerFeeResource\Pages\ListDealerFees;
use App\Filament\Resources\DealerFeeResource\Pages\CreateDealerFee;
use App\Filament\Resources\DealerFeeResource\Pages\EditDealerFee;
use App\Infrastructure\Eloquent\User\StkDealerFee;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\DealerFeeDetail;
use Filament\Tables\Enums\ActionsPosition;

class DealerFeeResource extends Resource
{
    protected static ?string $model            = StkDealerFee::class;
    protected static ?string $navigationIcon   = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::DEALER_FEE->value;
    protected static ?string $pluralModelLabel = '諸費用管理';
    protected static ?string $modelLabel       = '諸費用';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['dealer', 'dealer_staff']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('dealer_id', Auth::user()?->dealer_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('プラン名')
                ->required()
                ->maxLength(255)
                ->rules(['string']),

            Toggle::make('is_default')
                ->label('デフォルトプラン')
                ->default(false),

            TextInput::make('registration_fee')
                ->label('登録・手続き代行費用（円）')
                ->numeric()
                ->required()
                ->integer()
                ->minValue(0)
                ->rules(['integer', 'min:0']),

            TextInput::make('garage_cert_fee')
                ->label('車庫証明費用（円）')
                ->numeric()
                ->required()
                ->integer()
                ->minValue(0)
                ->rules(['integer', 'min:0']),

            TextInput::make('delivery_fee')
                ->label('納車費用（円）')
                ->numeric()
                ->required()
                ->integer()
                ->minValue(0)
                ->rules(['integer', 'min:0']),

            TextInput::make('maintenance_fee')
                ->label('整備費用（円）')
                ->numeric()
                ->required()
                ->integer()
                ->minValue(0)
                ->rules(['integer', 'min:0']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('プラン名')
                    ->sortable(),

                IconColumn::make('is_default')
                    ->label('デフォルト')
                    ->boolean(),

                TextColumn::make('registration_fee')
                    ->label('登録費用（円）')
                    ->money('JPY'),

                TextColumn::make('garage_cert_fee')
                    ->label('車庫証明（円）')
                    ->money('JPY'),

                TextColumn::make('delivery_fee')
                    ->label('納車費用（円）')
                    ->money('JPY'),

                TextColumn::make('maintenance_fee')
                    ->label('整備費用（円）')
                    ->money('JPY'),

                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (StkDealerFee $record) => DealerFeeDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index'     => ListDealerFees::route('/'),
            'create'    => CreateDealerFee::route('/create'),
        ];
    }
}
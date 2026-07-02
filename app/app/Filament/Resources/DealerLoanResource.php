<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\LoanMonths;
use App\Filament\Resources\DealerLoanResource\Pages\CreateDealerLoan;
use App\Filament\Resources\DealerLoanResource\Pages\EditDealerLoan;
use App\Filament\Resources\DealerLoanResource\Pages\ListDealerLoans;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Eloquent\User\StkDealerLoanPlan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Grid;

class DealerLoanResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = StkDealerLoanPlan::class;
    protected static ?string $navigationIcon   = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup  = 'ディーラーメニュー';
    protected static ?string $pluralModelLabel = 'ローンプラン管理';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('dealer_id', auth()->user()->dealer_id)
            ->withTrashed();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('プラン名')
                ->required()
                ->placeholder('例：通常ローン・オリコプラン')
                ->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('interest_rate')
                    ->label('金利（%）')
                    ->numeric()
                    ->step(0.1)
                    ->required()
                    ->suffix('%')
                    ->placeholder('例：3.9'),
            ]),

            Select::make('min_months')
                ->label('最小回数')
                ->options(LoanMonths::OPTIONS)
                ->required(),

            Select::make('max_months')
                ->label('最大回数')
                ->options(LoanMonths::OPTIONS)
                ->required(),

            TextInput::make('bonus_amount')
                ->label('ボーナス加算額（円）')
                ->numeric()
                ->placeholder('未入力の場合はボーナスなし'),

            TextInput::make('bonus_times')
                ->label('ボーナス回数（年）')
                ->numeric()
                ->placeholder('例：2（年2回）'),

            Textarea::make('note')
                ->label('備考')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('プラン名'),

                Tables\Columns\TextColumn::make('interest_rate')
                    ->label('金利')
                    ->formatStateUsing(fn ($state) => "{$state}%"),

                Tables\Columns\TextColumn::make('min_months')
                    ->label('最小回数')
                    ->formatStateUsing(fn ($state) => "{$state}回"),

                Tables\Columns\TextColumn::make('max_months')
                    ->label('最大回数')
                    ->formatStateUsing(fn ($state) => "{$state}回"),

                Tables\Columns\TextColumn::make('bonus_amount')
                    ->label('ボーナス加算')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state).'円' : '-'),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('ステータス')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => match(true) {
                        !is_null($record->deleted_at)          => '削除済み',
                        !is_null($record->delete_requested_at) => '削除申請中',
                        $state == 1                            => '有効',
                        default                                => '無効',
                    })
                    ->color(fn ($state, $record) => match(true) {
                        !is_null($record->deleted_at)          => 'gray',
                        !is_null($record->delete_requested_at) => 'warning',
                        $state == 1                            => 'success',
                        default                                => 'danger',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (StkDealerLoanPlan $record) => EditDealerLoan::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDealerLoans::route('/'),
            'create' => CreateDealerLoan::route('/create'),
            'edit'   => EditDealerLoan::route('/{record}/edit'),
        ];
    }
}
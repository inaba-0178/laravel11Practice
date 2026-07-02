<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DealerContentResource\Pages\ListDealerContents;
use App\Filament\Resources\DealerContentResource\Pages\CreateDealerContent;
use App\Filament\Resources\DealerContentResource\Pages\EditDealerContent;
use App\Filament\Pages\DealerContentDetail;
use App\Infrastructure\Eloquent\User\StkDealerContent;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DealerContentResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = StkDealerContent::class;
    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::DEALER_CONTENT->value;
    protected static ?string $pluralModelLabel = 'サービス・イベント・保証';
    protected static ?string $modelLabel       = 'コンテンツ';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('dealer_id', Auth::user()?->dealer_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category')
                ->label('カテゴリ')
                ->options([
                    'service'  => '各種サービス',
                    'event'    => 'フェア＆イベント',
                    'warranty' => '保証',
                ])
                ->required()
                ->reactive(),

            TextInput::make('title')
                ->label('タイトル')
                ->required()
                ->maxLength(255),

            TextInput::make('sort_order')
                ->label('表示順')
                ->numeric()
                ->default(0)
                ->minValue(0),

            Toggle::make('is_active')
                ->label('サイト表示')
                ->default(true),

            DatePicker::make('started_at')
                ->label('開始日')
                ->nullable()
                ->visible(fn (callable $get) => $get('category') === 'event'),

            DatePicker::make('ended_at')
                ->label('終了日')
                ->nullable()
                ->visible(fn (callable $get) => $get('category') === 'event'),

            Textarea::make('description')
                ->label('概要・説明')
                ->nullable()
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (StkDealerContent $record) => DealerContentDetail::getUrl(['id' => $record->id]))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('category')
                    ->label('カテゴリ')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'service'  => '各種サービス',
                        'event'    => 'フェア＆イベント',
                        'warranty' => '保証',
                        default    => '-',
                    })
                    ->sortable(),

                TextColumn::make('title')
                    ->label('タイトル')
                    ->sortable(),

                TextColumn::make('started_at')
                    ->label('開始日')
                    ->date('Y/m/d')
                    ->sortable(),

                TextColumn::make('ended_at')
                    ->label('終了日')
                    ->date('Y/m/d')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('表示順')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('サイト表示')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('更新日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (StkDealerContent $record) => DealerContentDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDealerContents::route('/'),
            'create' => CreateDealerContent::route('/create'),
            'edit'   => EditDealerContent::route('/{record}/edit'),
        ];
    }
}
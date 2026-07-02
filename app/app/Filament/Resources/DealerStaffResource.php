<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DealerStaffResource\Pages\ListDealerStaffs;
use App\Filament\Resources\DealerStaffResource\Pages\CreateDealerStaff;
use App\Filament\Resources\DealerStaffResource\Pages\EditDealerStaff;
use App\Filament\Pages\DealerStaffDetail;
use App\Infrastructure\Eloquent\User\StkDealerStaff;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DealerStaffResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = StkDealerStaff::class;
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::DEALER_STAFF->value;
    protected static ?string $pluralModelLabel = 'スタッフ管理';
    protected static ?string $modelLabel       = 'スタッフ';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('dealer_id', Auth::user()?->dealer_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('スタッフ名')
                ->required()
                ->maxLength(100),

            TextInput::make('position')
                ->label('役職')
                ->nullable()
                ->maxLength(100),

            TextInput::make('sort_order')
                ->label('表示順')
                ->numeric()
                ->default(0)
                ->minValue(0),

            Toggle::make('is_active')
                ->label('サイト表示')
                ->default(true),

            Textarea::make('comment')
                ->label('自己紹介・コメント')
                ->nullable()
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (StkDealerStaff $record) => DealerStaffDetail::getUrl(['id' => $record->id]))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('スタッフ名')
                    ->sortable(),

                TextColumn::make('position')
                    ->label('役職'),

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
                    ->url(fn (StkDealerStaff $record) => DealerStaffDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDealerStaffs::route('/'),
            'create' => CreateDealerStaff::route('/create'),
            'edit'   => EditDealerStaff::route('/{record}/edit'),
        ];
    }
}
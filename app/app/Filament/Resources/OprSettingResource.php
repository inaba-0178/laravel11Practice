<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Resources\OprSettingResource\Pages\CreateOprSetting;
use App\Filament\Resources\OprSettingResource\Pages\EditOprSetting;
use App\Filament\Resources\OprSettingResource\Pages\ListOprSettings;
use App\Infrastructure\Eloquent\Opr\OprSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;

class OprSettingResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = OprSetting::class;
    protected static ?string $navigationIcon   = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup  = NavigationGroup::OPR_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::OPR_SETTINGS->value;
    protected static ?string $pluralModelLabel = 'システム設定';
    protected static ?string $modelLabel       = 'システム設定';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([

                TextInput::make('key')
                    ->label('設定キー')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),

                TextInput::make('key')
                    ->label('設定キー')
                    ->required()
                    ->maxLength(100)
                    ->unique(OprSetting::class, 'key')
                    ->helperText('例：view_count_delay_seconds')
                    ->visibleOn('create'),

                TextInput::make('label')
                    ->label('表示名')
                    ->required()
                    ->maxLength(255),

                TextInput::make('value')
                    ->label('設定値')
                    ->required()
                    ->maxLength(255)
                    ->suffix(function ($get) {
                        return match(true) {
                            str_ends_with($get('key') ?? '', '_seconds') => '秒',
                            str_ends_with($get('key') ?? '', '_minutes') => '分',
                            str_ends_with($get('key') ?? '', '_hours')   => '時間',
                            str_ends_with($get('key') ?? '', '_days')    => '日',
                            default                                       => '',
                        };
                    }),

                Textarea::make('description')
                    ->label('説明')
                    ->nullable()
                    ->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('設定キー')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('label')
                    ->label('表示名')
                    ->searchable(),

                TextColumn::make('value')
                    ->label('設定値')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($state, $record) {
                        $unit = match(true) {
                            str_ends_with($record->key, '_seconds') => '秒',
                            str_ends_with($record->key, '_minutes') => '分',
                            str_ends_with($record->key, '_hours')   => '時間',
                            str_ends_with($record->key, '_days')    => '日',
                            default                                  => '',
                        };
                        return $state . ($unit ? " {$unit}" : '');
                    }),

                TextColumn::make('description')
                    ->label('説明')
                    ->limit(50)
                    ->placeholder('-'),

                TextColumn::make('updated_at')
                    ->label('最終更新')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'asc')
            ->actions([
                EditAction::make()->label('編集'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOprSettings::route('/'),
            'create' => CreateOprSetting::route('/create'),
            'edit'   => EditOprSetting::route('/{record}/edit'),
        ];
    }
}
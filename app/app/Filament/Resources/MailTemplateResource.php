<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailTemplateResource\Pages;
use App\Infrastructure\Eloquent\Opr\OprMailTemplate;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;

class MailTemplateResource extends Resource
{
    protected static ?string $model = OprMailTemplate::class;

    protected static ?string    $navigationIcon     = 'heroicon-o-envelope';
    protected static ?string    $navigationGroup    = NavigationGroup::OPR_GROUP->value;
    protected static ?int       $navigationSort     = NavigationSort::OPR_MAIL_TEMPLATE->value;
    protected static ?string    $pluralModelLabel   = 'メールテンプレート一覧';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('template_name')
                    ->label('テンプレート名')
                    ->required()
                    ->maxLength(255),

                TextInput::make('subject')
                    ->label('件名')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('body')
                    ->label('本文')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'blockquote',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('template_name')
                    ->label('テンプレート名')
                    ->searchable(),

                TextColumn::make('subject')
                    ->label('件名')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('更新日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()->label('詳細'),
                EditAction::make()->label('編集'),
                DeleteAction::make()->label('削除'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMailTemplates::route('/'),
            'create' => Pages\CreateMailTemplate::route('/create'),
            'view'   => Pages\ViewMailTemplate::route('/{record}'),
            'edit'   => Pages\EditMailTemplate::route('/{record}/edit'),
        ];
    }
}
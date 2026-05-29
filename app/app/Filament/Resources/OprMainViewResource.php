<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OprMainViewResource\Pages\ListOprMainViews;
use App\Filament\Resources\OprMainViewResource\Pages\CreateOprMainView;
use App\Filament\Resources\OprMainViewResource\Pages\EditOprMainView;
use App\Filament\Pages\OprMainViewDetail;
use App\Infrastructure\Eloquent\Opr\OprMainView;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class OprMainViewResource extends Resource
{
    protected static ?string $model            = OprMainView::class;
    protected static ?string $navigationIcon   = 'heroicon-o-photo';
    protected static ?string $pluralModelLabel = 'メインビュー';
    protected static ?string $modelLabel       = 'メインビュー';
    protected static ?string $navigationGroup  = NavigationGroup::OPR_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::OPR_MAIN_VIEW->value;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Placeholder::make('image_notice')
                ->label('')
                ->content(new HtmlString('
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                        ⚠️ 画像は登録後の詳細ページからアップロードできます。
                    </div>
                    <div class="p-4 mt-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700">
                        📌 表示ルール<br>
                        ・<strong>有効</strong> かつ <strong>期間内</strong> の場合のみTOPページに表示されます。<br>
                        ・無効の場合は期間設定に関わらず表示されません。<br>
                        ・期間を設定しない場合は有効な間は常に表示されます。
                    </div>
                '))
                ->visibleOn('create')
                ->columnSpanFull(),

            Placeholder::make('edit_notice')
                ->label('')
                ->content(new HtmlString('
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                        ⚠️ 画像パスは自動設定されるため編集不可です。
                    </div>
                    <div class="p-4 mt-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700">
                        📌 表示ルール<br>
                        ・<strong>有効</strong> かつ <strong>期間内</strong> の場合のみTOPページに表示されます。<br>
                        ・無効の場合は期間設定に関わらず表示されません。<br>
                        ・期間を設定しない場合は有効な間は常に表示されます。
                    </div>
                '))
                ->visibleOn('edit')
                ->columnSpanFull(),

            TextInput::make('title')
                ->label('タイトル')
                ->maxLength(255),
            TextInput::make('sub')
                ->label('サブテキスト')
                ->maxLength(500),
            TextInput::make('label')
                ->label('ラベル')
                ->maxLength(255),
            TextInput::make('image_path')
                ->label('画像パス ※画像パスは自動設定されるため不要修正できないようにしています')
                ->disabled()
                ->dehydrated(false),
            TextInput::make('link_url')
                ->label('リンクURL')
                ->maxLength(255),
            TextInput::make('sort_order')
                ->label('表示順')
                ->numeric()
                ->default(1000),
            Toggle::make('is_active')
                ->label('有効')
                ->default(true),
            DateTimePicker::make('start_at')
                ->label('表示開始日時'),
            DateTimePicker::make('end_at')
                ->label('表示終了日時'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (OprMainView $record) => OprMainViewDetail::getUrl(['id' => $record->id]))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')->label('タイトル'),
                TextColumn::make('label')->label('ラベル'),
                IconColumn::make('is_active')->label('有効')->boolean(),
                TextColumn::make('sort_order')->label('表示順')->sortable(),
                TextColumn::make('start_at')->label('開始日時')->dateTime('Y/m/d H:i'),
                TextColumn::make('end_at')->label('終了日時')->dateTime('Y/m/d H:i'),
                TextColumn::make('updated_at')->label('更新日時')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (OprMainView $record) => OprMainViewDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOprMainViews::route('/'),
            'create' => CreateOprMainView::route('/create'),
            'edit'   => EditOprMainView::route('/{record}/edit'),
        ];
    }
}
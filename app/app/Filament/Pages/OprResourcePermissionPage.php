<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use App\Domain\Common\Services\PermissionCacheService;
use App\Infrastructure\Eloquent\Opr\OprPermissionLog;
use App\Infrastructure\Eloquent\Opr\OprResourcePermission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OprResourcePermissionPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-lock-closed';
    protected static string  $view            = 'filament.pages.opr-resource-permission';
    protected static ?string $navigationGroup = NavigationGroup::SYSTEM_GROUP->value;
    protected static ?string $title           = '権限管理';
    protected static ?int    $navigationSort  = 900;

    // superのみアクセス可能（DBではなくハードコード）
    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(OprResourcePermission::query()->orderBy('resource_group')->orderBy('resource_label'))
            ->columns([
                TextColumn::make('resource_group')
                    ->label('グループ')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('resource_label')
                    ->label('機能名')
                    ->sortable(),
                TextColumn::make('resource_key')
                    ->label('リソースキー')
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('allowed_roles')
                    ->label('許可ロール')
                    ->getStateUsing(fn(OprResourcePermission $record) => implode('・', array_map(
                        fn($r) => RoleConstants::LABELS[$r] ?? $r,
                        $record->allowed_roles ?? []
                    ))),
                TextColumn::make('updated_at')
                    ->label('最終更新')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('resource_group')
                    ->label('グループ')
                    ->options(
                        OprResourcePermission::query()
                            ->distinct()
                            ->pluck('resource_group', 'resource_group')
                            ->toArray()
                    ),
                Filter::make('resource_label')
                    ->label('機能名')
                    ->form([
                        TextInput::make('resource_label')
                            ->label('機能名')
                            ->placeholder('キーワード検索'),
                    ])
                    ->query(fn(Builder $query, array $data) =>
                        $query->when($data['resource_label'] ?? null,
                            fn($q, $v) => $q->where('resource_label', 'like', "%{$v}%")
                        )
                    ),
                SelectFilter::make('allowed_role')
                    ->label('ロール（含む）')
                    ->options(collect(RoleConstants::LABELS)->except(RoleConstants::SUPER)->toArray())
                    ->query(fn(Builder $query, array $data) =>
                        $query->when($data['value'] ?? null,
                            fn($q, $v) => $q->whereJsonContains('allowed_roles', $v)
                        )
                    ),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(Action $action) => $action->label('適用'))
            ->headerActions([
                Action::make('bulk_group')
                    ->label('グループ一括設定')
                    ->icon('heroicon-o-user-group')
                    ->color('warning')
                    ->form([
                        Select::make('resource_group')
                            ->label('グループ')
                            ->options(
                                OprResourcePermission::query()
                                    ->distinct()
                                    ->orderBy('resource_group')
                                    ->pluck('resource_group', 'resource_group')
                                    ->toArray()
                            )
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (!$state) return;
                                $roles = OprResourcePermission::where('resource_group', $state)
                                    ->first()?->allowed_roles ?? [];
                                $set('allowed_roles', $roles);
                            })
                            ->helperText('選択するとそのグループの現在の設定を読み込みます'),
                        CheckboxList::make('allowed_roles')
                            ->label('許可ロール（グループ内全件に適用）')
                            ->options(collect(RoleConstants::LABELS)->except(RoleConstants::SUPER)->toArray())
                            ->columns(2),
                    ])
                    ->action(function (array $data): void {
                        $user    = auth()->user();
                        $group   = $data['resource_group'];
                        $roles   = $data['allowed_roles'] ?? [];
                        $records = OprResourcePermission::where('resource_group', $group)->get();

                        foreach ($records as $record) {
                            OprPermissionLog::create([
                                'resource_key'    => $record->resource_key,
                                'changed_by'      => $user->id,
                                'changed_by_role' => $user->role,
                                'before_roles'    => $record->allowed_roles,
                                'after_roles'     => $roles,
                            ]);
                        }

                        OprResourcePermission::where('resource_group', $group)
                            ->update(['allowed_roles' => json_encode($roles)]);

                        PermissionCacheService::clearCache();

                        Notification::make()
                            ->title("{$group} の権限を一括更新しました（{$records->count()}件）")
                            ->success()
                            ->send();
                    }),
                Action::make('create')
                    ->label('新規追加')
                    ->icon('heroicon-o-plus')
                    ->form(function () {
                        $registered = OprResourcePermission::pluck('resource_key')->toArray();
                        [$options, $labelMap, $groupMap] = $this->scanFilamentClasses($registered);

                        return [
                            Select::make('resource_key')
                                ->label('機能')
                                ->options($options)
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (?string $state, Set $set) use ($labelMap, $groupMap): void {
                                    $set('resource_label', $labelMap[$state] ?? '');
                                    $set('resource_group', $groupMap[$state] ?? '');
                                })
                                ->helperText('和名で検索できます'),
                            TextInput::make('resource_label')
                                ->label('機能名（日本語）')
                                ->required(),
                            TextInput::make('resource_group')
                                ->label('グループ')
                                ->required()
                                ->datalist(
                                    OprResourcePermission::query()
                                        ->distinct()
                                        ->pluck('resource_group')
                                        ->toArray()
                                ),
                            CheckboxList::make('allowed_roles')
                                ->label('許可ロール')
                                ->options(collect(RoleConstants::LABELS)->except(RoleConstants::SUPER)->toArray())
                                ->columns(2),
                        ];
                    })
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        OprResourcePermission::create([
                            'resource_key'   => $data['resource_key'],
                            'resource_label' => $data['resource_label'],
                            'resource_group' => $data['resource_group'],
                            'allowed_roles'  => $data['allowed_roles'] ?? [],
                        ]);

                        OprPermissionLog::create([
                            'resource_key'    => $data['resource_key'],
                            'changed_by'      => $user->id,
                            'changed_by_role' => $user->role,
                            'before_roles'    => [],
                            'after_roles'     => $data['allowed_roles'] ?? [],
                        ]);

                        PermissionCacheService::clearCache();

                        Notification::make()
                            ->title('権限を追加しました')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('edit')
                    ->label('編集')
                    ->icon('heroicon-o-pencil')
                    ->form(fn(OprResourcePermission $record) => [
                        CheckboxList::make('allowed_roles')
                            ->label('許可ロール')
                            ->options($this->getRoleOptions())
                            ->default($record->allowed_roles)
                            ->columns(2),
                    ])
                    ->action(function (OprResourcePermission $record, array $data): void {
                        $user      = auth()->user();
                        $beforeRoles = $record->allowed_roles;
                        $afterRoles  = $data['allowed_roles'];

                        // superロールの付与・剥奪はsuperのみ（念のための二重チェック）
                        if ($user->role !== RoleConstants::SUPER) {
                            $afterRoles = array_filter(
                                $afterRoles,
                                fn($r) => $r !== RoleConstants::SUPER
                            );
                        }

                        $record->update(['allowed_roles' => array_values($afterRoles)]);

                        OprPermissionLog::create([
                            'resource_key'    => $record->resource_key,
                            'changed_by'      => $user->id,
                            'changed_by_role' => $user->role,
                            'before_roles'    => $beforeRoles,
                            'after_roles'     => array_values($afterRoles),
                        ]);

                        PermissionCacheService::clearCache();

                        Notification::make()
                            ->title('権限を更新しました')
                            ->success()
                            ->send();
                    }),
            ], position: ActionsPosition::BeforeColumns)
            ->paginated(false);
    }

    /**
     * 未登録のFilamentクラスをスキャンして選択肢を生成
     * @return array{0: array<string,string>, 1: array<string,string>, 2: array<string,string>}
     */
    private function scanFilamentClasses(array $registered): array
    {
        $options  = [];
        $labelMap = [];
        $groupMap = [];

        $dirs = [
            'Resource' => glob(app_path('Filament/Resources/*.php')) ?: [],
            'Page'     => glob(app_path('Filament/Pages/*.php')) ?: [],
        ];

        foreach ($dirs as $type => $files) {
            foreach ($files as $file) {
                $basename = pathinfo($file, PATHINFO_FILENAME);

                if (in_array($basename, $registered, true)) continue;
                if ($basename === 'OprResourcePermissionPage') continue;

                $content = file_get_contents($file);

                // 日本語ラベルを抽出
                if ($type === 'Resource') {
                    preg_match('/\$pluralModelLabel\s*=\s*[\'"](.+?)[\'"]/u', $content, $m);
                } else {
                    preg_match('/\$title\s*=\s*[\'"](.+?)[\'"]/u', $content, $m);
                }
                $label = !empty(trim($m[1] ?? '')) ? $m[1] : $basename;

                // ナビゲーショングループを抽出
                preg_match('/\$navigationGroup\s*=\s*[\'"](.+?)[\'"]/u', $content, $gm);
                $group = $gm[1] ?? '';

                $options[$basename]  = $label . '　／　' . $basename;
                $labelMap[$basename] = $label;
                $groupMap[$basename] = $group;
            }
        }

        asort($options);
        return [$options, $labelMap, $groupMap];
    }

    private function getRoleOptions(): array
    {
        $options = RoleConstants::LABELS;

        // superは常に全アクセス可能なので選択肢から除外（設定しても意味がないため）
        unset($options[RoleConstants::SUPER]);

        return $options;
    }
}

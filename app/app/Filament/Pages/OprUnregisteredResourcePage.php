<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use App\Domain\Common\Services\PermissionCacheService;
use App\Infrastructure\Eloquent\Opr\OprPermissionLog;
use App\Infrastructure\Eloquent\Opr\OprResourcePermission;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class OprUnregisteredResourcePage extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $navigationIcon  = 'heroicon-o-exclamation-triangle';
    protected static string  $view            = 'filament.pages.opr-unregistered-resource';
    protected static ?string $navigationGroup = NavigationGroup::SYSTEM_GROUP->value;
    protected static ?string $title           = '未登録ページ検出';
    protected static ?int    $navigationSort  = 903;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public function getUnregisteredClasses(): Collection
    {
        $registered = OprResourcePermission::pluck('resource_key')->toArray();
        $items      = collect();

        $dirs = [
            'Resource' => glob(app_path('Filament/Resources/*.php')) ?: [],
            'Page'     => glob(app_path('Filament/Pages/*.php')) ?: [],
        ];

        foreach ($dirs as $type => $files) {
            foreach ($files as $file) {
                $basename = pathinfo($file, PATHINFO_FILENAME);

                if (in_array($basename, $registered, true)) continue;

                if (str_ends_with($basename, 'Detail')) continue;
                if (str_ends_with($basename, 'DetailPage')) continue;

                $content = file_get_contents($file);

                // 独自 canAccess() を持つページはDB管理外（スーパー固定等）のためスキップ
                if (str_contains($content, 'function canAccess(')) continue;
                $m       = [];
                $gm      = [];

                if ($type === 'Resource') {
                    preg_match('/\$pluralModelLabel\s*=\s*[\'"](.+?)[\'"]/u', $content, $m);
                } else {
                    preg_match('/\$title\s*=\s*[\'"](.+?)[\'"]/u', $content, $m);
                }
                $label = !empty(trim($m[1] ?? '')) ? $m[1] : $basename;

                preg_match('/\$navigationGroup\s*=\s*[\'"](.+?)[\'"]/u', $content, $gm);
                $group = $gm[1] ?? '未設定';

                $items->push([
                    'resource_key'   => $basename,
                    'resource_label' => $label,
                    'resource_group' => $group,
                ]);
            }
        }

        return $items->sortBy(['resource_group', 'resource_label'])->values();
    }

    public function openRegisterModal(string $resourceKey, string $resourceLabel, string $resourceGroup): void
    {
        $this->mountAction('register', [
            'resource_key'   => $resourceKey,
            'resource_label' => $resourceLabel,
            'resource_group' => $resourceGroup,
        ]);
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->label('権限登録')
            ->icon('heroicon-o-plus')
            ->color('success')
            ->fillForm(fn(array $arguments) => [
                'resource_key'   => $arguments['resource_key'] ?? '',
                'resource_label' => $arguments['resource_label'] ?? '',
                'resource_group' => $arguments['resource_group'] ?? '',
            ])
            ->form([
                TextInput::make('resource_label')
                    ->label('機能名')
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('resource_key')
                    ->label('リソースキー')
                    ->disabled()
                    ->dehydrated()
                    ->extraInputAttributes(['class' => 'font-mono text-sm']),
                Hidden::make('resource_group'),
                CheckboxList::make('allowed_roles')
                    ->label('許可ロール')
                    ->options(collect(RoleConstants::LABELS)->except(RoleConstants::SUPER)->toArray())
                    ->columns(2),
            ])
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
                    ->title($data['resource_label'] . ' を登録しました')
                    ->success()
                    ->send();
            });
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Constants\RoleConstants;
use App\Constants\Role\RoleManagement;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class UserDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon           = 'heroicon-o-user';
    protected static string  $view                     = 'filament.pages.user-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string $id     = null;
    public ?User   $record = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = User::whereNull('deleted_at')->findOrFail($this->id);

        $authUser = Auth::user();

        // 自分より高いロールのユーザーは見れない
        $lowerOrEqual = RoleConstants::getLowerOrEqualRoles($authUser->role);
        if (!in_array($this->record->role, $lowerOrEqual)) {
            abort(403);
        }

        if (in_array($authUser->role, RoleManagement::DEALER_ROLES)) {
            if ($this->record->dealer_id !== $authUser->dealer_id) {
                abort(403);
            }
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            UserResource::getUrl() => 'ユーザー管理',
            UserDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return 'ユーザー : [' . $this->record->name . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'         => $this->record->id,
            'name'       => $this->record->name,
            'email'      => $this->record->email,
            'role'       => RoleConstants::LABELS[$this->record->role] ?? $this->record->role,
            'position'   => $this->record->position ?? '-',
            'created_at' => $this->record->created_at,
            'updated_at' => $this->record->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('ユーザー情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('名前'),
                        TextEntry::make('email')->label('メールアドレス'),
                        TextEntry::make('role')->label('ロール'),
                        TextEntry::make('position')->label('役職'),
                    ]),

                Section::make('日時')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('編集')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('ユーザーを編集しますか？')
                ->modalDescription('編集画面に移動します。')
                ->modalSubmitActionLabel('編集する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    $this->redirect(UserResource::getUrl('edit', ['record' => $this->record->id]));
                }),

            Action::make('delete')
                ->label('削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('ユーザーを削除しますか？')
                ->modalDescription('削除すると元に戻せません。本当に削除しますか？')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    $this->record->delete();

                    Notification::make()
                        ->title('削除しました')
                        ->success()
                        ->send();

                    $this->redirect(UserResource::getUrl('index'));
                }),
        ];
    }
}
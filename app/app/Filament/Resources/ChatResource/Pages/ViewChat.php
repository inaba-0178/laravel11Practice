<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatResource\Pages;

use App\Domain\Shared\Constants\MailTemplateKey;
use App\Domain\Shared\Constants\UserType;
use App\Filament\Resources\ChatResource;
use App\Infrastructure\Eloquent\User\Message;
use App\Infrastructure\Eloquent\User\MessageRead;
use App\Infrastructure\Eloquent\User\Room;
use App\Infrastructure\Eloquent\User\RoomUser;
use App\Infrastructure\Eloquent\User\RoomUserDeletionLog;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Infrastructure\Events\MessageRead as MessageReadEvent;
use App\Infrastructure\Events\MessageSent;
use App\Application\Services\MailService;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewChat extends Page
{
    protected static string $resource = ChatResource::class;
    protected static string $view     = 'filament.pages.view-chat';

    public Room    $record;
    public string  $newMessage          = '';
    public array   $messages            = [];
    public ?int    $deletingRoomUserId  = null;
    public string  $deleteReason        = '';
    public ?string $deleteReasonDetail  = null;
    public bool    $showDeletionLogs    = false;

    // ロール定数
    private const EDITABLE_ROLES = ['dealer', 'dealer_staff'];
    private const READONLY_ROLES = ['super', 'admin'];

    public function mount(Room $record): void
    {
        $this->record = $record->load(['roomUsers']);
        $this->loadMessages();
    }

    public function getTitle(): string
    {
        return $this->record->name ?? 'チャット';
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            Action::make('back')
                ->label('一覧に戻る')
                ->color('gray')
                ->url(ListChats::getUrl()),
        ];

        if ($this->canEdit()) {
            $actions[] = $this->addStaffAction();
            $actions[] = $this->addMemberAction();
        }

        return $actions;
    }

    private function canEdit(): bool
    {
        return in_array(Auth::user()?->role, self::EDITABLE_ROLES);
    }

    private function canSend(): bool
    {
        $user = Auth::user();
        if (in_array($user->role, self::READONLY_ROLES) && !$user->dealer_id) {
            return false;
        }
        return true;
    }

    private function addStaffAction(): Action
    {
        return Action::make('add_staff')
            ->label('担当者を追加')
            ->color('info')
            ->icon('heroicon-o-user-plus')
            ->form([
                Select::make('user_id')
                    ->label('担当者')
                    ->options(function () {
                        $existingIds = $this->record->roomUsers
                            ->where('user_type', UserType::STAFF)
                            ->pluck('user_id')
                            ->toArray();

                        return User::where('is_active', 1)
                            ->where('dealer_id', Auth::user()->dealer_id)
                            ->whereNotIn('id', $existingIds)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),
            ])
            ->action(function (array $data) {
                $this->record->roomUsers()->create([
                    'user_id'      => (string) $data['user_id'],
                    'user_type'    => UserType::STAFF,
                    'status'       => 'approved',
                    'invited_at'   => now(),
                    'responded_at' => now(),
                ]);

                $this->record->load(['roomUsers']);

                Notification::make()->title('担当者を追加しました')->success()->send();
            });
    }

    private function addMemberAction(): Action
    {
        return Action::make('add_member')
            ->label('ユーザーを追加')
            ->color('warning')
            ->icon('heroicon-o-user-plus')
            ->form([
                TextInput::make('email')
                    ->label('メールアドレス')
                    ->email()
                    ->required()
                    ->placeholder('招待するユーザーのメールアドレスを入力'),

                Placeholder::make('notice')
                    ->label('')
                    ->content('※入力されたメールアドレスに招待メールを送信します。'),
            ])
            ->action(function (array $data) {
                $expireHours = (int) DB::connection('mst')
                    ->table('opr_settings')
                    ->where('key', 'chat_invite_expire_hours')
                    ->value('value') ?? 72;

                $user = UsrUser::where('email', $data['email'])->first();

                if ($user) {
                    $exists = $this->record->roomUsers()
                        ->where('user_id', $user->id)
                        ->where('user_type', UserType::MEMBER)
                        ->exists();

                    if (!$exists) {
                        $this->record->roomUsers()->create([
                            'user_id'    => $user->id,
                            'user_type'  => UserType::MEMBER,
                            'status'     => 'pending',
                            'invited_at' => now(),
                            'expired_at' => now()->addHours($expireHours),
                        ]);

                        app(MailService::class)->send(
                            templateKey:  MailTemplateKey::CHAT_INVITED,
                            toEmail:      $data['email'],
                            placeholders: [
                                'user_name'   => $user->display_name,
                                'dealer_name' => Auth::user()->name,
                                'url'         => config('app.frontend_url') . '/chat/invite?room=' . $this->record->id,
                            ],
                        );
                    }

                    $this->record->load(['roomUsers']);
                }

                Notification::make()->title('招待メールを送信しました')->success()->send();
            });
    }

    public function loadMessages(): void
    {
        $authId = (string) Auth::id();

        $messages = Message::where('room_id', $this->record->id)
            ->with('messageReads')
            ->orderBy('created_at')
            ->get();

        // 未読メッセージを既読にする
        $unreadIds = $messages
            ->filter(fn ($m) =>
                !($m->user_id === $authId && $m->user_type === UserType::STAFF)
                && !$m->messageReads->contains(
                    fn ($r) => $r->user_id === $authId && $r->user_type === UserType::STAFF
                )
            )
            ->pluck('id')
            ->toArray();

        if (!empty($unreadIds)) {
            foreach ($unreadIds as $messageId) {
                MessageRead::firstOrCreate(
                    [
                        'message_id' => $messageId,
                        'user_id'    => $authId,
                        'user_type'  => UserType::STAFF,
                    ],
                    ['read_at' => now()]
                );
            }

            broadcast(new MessageReadEvent(
                roomId:     $this->record->id,
                userId:     $authId,
                userType:   UserType::STAFF,
                messageIds: $unreadIds,
            ));
        }

        $this->messages = $messages->map(function ($message) use ($authId) {
            $sender = $this->getSender($message->user_id, $message->user_type);
            return [
                'id'         => $message->id,
                'message'    => $message->message,
                'user_id'    => $message->user_id,
                'user_type'  => $message->user_type,
                'role'       => UserType::getRole($message->user_type, $sender?->role),
                'user_name'  => UserType::getDisplayName($sender, $message->user_type),
                'created_at' => $message->created_at->format('H:i'),
                'date'       => $message->created_at->format('Y/m/d'), // 追加
                'is_mine'    => $message->user_id === $authId && $message->user_type === UserType::STAFF,
                'read_count' => $message->messageReads
                    ->filter(fn ($r) => $r->user_id !== $message->user_id)
                    ->count(),
            ];
        })->toArray();

        $this->dispatch('messages-updated');
    }

    private function getSender(string $userId, string $userType): ?object
    {
        return $userType === UserType::STAFF
            ? User::find($userId)
            : UsrUser::find($userId);
    }

    public function getParticipants(): array
    {
        return $this->record->roomUsers
            ->map(fn ($roomUser) => [
                'id'           => $roomUser->user_id,
                'room_user_id' => $roomUser->id,
                'user_type'    => $roomUser->user_type,
                'role'         => UserType::getRole($roomUser->user_type, $this->getSender($roomUser->user_id, $roomUser->user_type)?->role),
                'status'       => $roomUser->status ?? 'approved',
                'name'         => UserType::getDisplayName($this->getSender($roomUser->user_id, $roomUser->user_type), $roomUser->user_type),
                'email'        => $this->getSender($roomUser->user_id, $roomUser->user_type)?->email ?? '-',
                'type_label'   => $roomUser->user_type === UserType::STAFF ? 'ディーラー' : 'ユーザー',
            ])
            ->toArray();
    }

    public function removeParticipant(int $roomUserId): void
    {
        if (!$this->canEdit()) {
            Notification::make()->title('権限がありません')->danger()->send();
            return;
        }
        $this->deletingRoomUserId = $roomUserId;
    }

    public function confirmDelete(): void
    {
        if (!$this->deletingRoomUserId || empty($this->deleteReason)) {
            Notification::make()->title('削除理由を選択してください')->warning()->send();
            return;
        }

        $roomUser = RoomUser::find($this->deletingRoomUserId);
        if (!$roomUser) return;

        RoomUserDeletionLog::create([
            'room_id'           => $this->record->id,
            'deleted_user_id'   => $roomUser->user_id,
            'deleted_user_type' => $roomUser->user_type,
            'deleted_by_id'     => (string) Auth::id(),
            'deleted_by_type'   => UserType::STAFF,
            'reason'            => $this->deleteReason,
            'reason_detail'     => $this->deleteReasonDetail,
        ]);

        $roomUser->delete();
        $this->record->load(['roomUsers']);
        $this->resetDeleteState();

        Notification::make()->title('参加者を削除しました')->success()->send();
    }

    public function cancelDelete(): void
    {
        $this->resetDeleteState();
    }

    private function resetDeleteState(): void
    {
        $this->deletingRoomUserId = null;
        $this->deleteReason       = '';
        $this->deleteReasonDetail = null;
    }

    public function getDeletionLogs(): array
    {
        return RoomUserDeletionLog::where('room_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($log) => [
                'deleted_user_name' => UserType::getDisplayName(
                    $this->getSender($log->deleted_user_id, $log->deleted_user_type),
                    $log->deleted_user_type
                ),
                'deleted_by_name'   => $this->getSender($log->deleted_by_id, $log->deleted_by_type)?->name ?? '不明',
                'reason'            => $log->reason,
                'reason_detail'     => $log->reason_detail,
                'deleted_at'        => $log->created_at->format('Y/m/d H:i'),
            ])
            ->toArray();
    }

    public function sendMessage(): void
    {
        if (!$this->canSend()) {
            Notification::make()->title('送信権限がありません')->danger()->send();
            return;
        }

        if (empty(trim($this->newMessage))) return;

        $message = Message::create([
            'room_id'   => $this->record->id,
            'user_id'   => (string) Auth::id(),
            'user_type' => UserType::STAFF,
            'message'   => $this->newMessage,
        ]);

        broadcast(new MessageSent(
            roomId:    $this->record->id,
            userId:    (string) Auth::id(),
            userType:  UserType::STAFF,
            id:        $message->id,
            message:   $this->newMessage,
            createdAt: now()->toISOString(),
            userModel: Auth::user(),
        ));

        $this->newMessage = '';
        $this->loadMessages();

        Notification::make()->title('送信しました')->success()->send();
    }

    public function onMessageReceived(): void
    {
        $this->loadMessages();
    }

    public function getListeners(): array
    {
        return [
            "echo:room.{$this->record->id},.message.sent" => 'onMessageReceived',
        ];
    }
}
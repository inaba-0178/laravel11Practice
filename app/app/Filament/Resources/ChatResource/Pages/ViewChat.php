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
use App\Application\Services\AttachmentService;

class ViewChat extends Page
{
    protected static string $resource = ChatResource::class;
    protected static string $view     = 'filament.pages.view-chat';

    public Room     $record;
    public string   $newMessage             = '';
    public array    $messages               = [];
    public ?int     $deletingRoomUserId     = null;
    public string   $deleteReason           = '';
    public ?string  $deleteReasonDetail     = null;
    public bool     $showDeletionLogs       = false;
    public array    $attachments            = [];
    public array    $participants           = [];
    public array    $deletionLogs           = [];
    public bool     $canEdit                = false;
    public bool     $canSend                = false;
    public bool     $canViewLog             = false;
    public string   $currentUserId          = '';

    // ロール定数
    private const EDITABLE_ROLES = ['dealer', 'dealer_staff'];
    private const READONLY_ROLES = ['super', 'admin'];

    public function mount(Room $record): void
    {
        $this->record        = $record->load(['roomUsers']);
        $this->currentUserId = (string) Auth::id();
        $this->canEdit       = in_array(Auth::user()?->role, self::EDITABLE_ROLES);
        $this->canSend       = !(in_array(Auth::user()?->role, self::READONLY_ROLES) && !Auth::user()?->dealer_id);
        $this->canViewLog    = in_array(Auth::user()?->role, [...self::EDITABLE_ROLES, ...self::READONLY_ROLES]);
        $this->loadMessages();
        $this->loadParticipants();
        $this->loadDeletionLogs();
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

        if ($this->canEdit) {
            $actions[] = $this->addStaffAction();
            $actions[] = $this->addMemberAction();
        }

        return $actions;
    }

    private static function resolveBadgeColor(string $role, string $userType): string
    {
        return match(true) {
            $role === 'super'               => '#d97706',
            $role === 'admin'               => '#dc2626',
            $userType === UserType::STAFF   => '#185FA5',
            default                         => '#dc5078',
        };
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
                $this->loadParticipants();

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
                    $this->loadParticipants();
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
            $sender   = $this->getSender($message->user_id, $message->user_type);
            $role     = UserType::getRole($message->user_type, $sender?->role);
            $userType = $message->user_type;
            $size     = $message->attachment_size;
            return [
                'id'                    => $message->id,
                'message'               => $message->message,
                'user_id'               => $message->user_id,
                'user_type'             => $userType,
                'role'                  => $role,
                'user_name'             => UserType::getDisplayName($sender, $userType),
                'created_at'            => $message->created_at->format('H:i'),
                'date'                  => $message->created_at->format('Y/m/d'),
                'is_mine'               => $message->user_id === $authId && $userType === UserType::STAFF,
                'read_count'            => $message->messageReads
                    ->filter(fn ($r) => $r->user_id !== $message->user_id)
                    ->count(),
                'attachment_url'        => $message->attachment_url,
                'attachment_type'       => $message->attachment_type,
                'attachment_name'       => $message->attachment_name,
                'attachment_size'       => $size,
                'attachment_size_label' => $size === null ? '' : ($size < 1024 ? "{$size}B" : ($size < 1048576 ? round($size / 1024, 1) . 'KB' : round($size / 1048576, 1) . 'MB')),
                'badge_color'           => self::resolveBadgeColor($role, $userType),
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

    private function loadParticipants(): void
    {
        $this->participants = $this->record->roomUsers
            ->map(function ($roomUser) {
                $sender = $this->getSender($roomUser->user_id, $roomUser->user_type);
                $role   = UserType::getRole($roomUser->user_type, $sender?->role);
                $status = $roomUser->status ?? 'approved';
                return [
                    'id'           => $roomUser->user_id,
                    'room_user_id' => $roomUser->id,
                    'user_type'    => $roomUser->user_type,
                    'role'         => $role,
                    'status'       => $status,
                    'name'         => UserType::getDisplayName($sender, $roomUser->user_type),
                    'email'        => $sender?->email ?? '-',
                    'type_label'   => $roomUser->user_type === UserType::STAFF ? 'ディーラー' : 'ユーザー',
                    'badge_color'  => self::resolveBadgeColor($role, $roomUser->user_type),
                    'status_label' => match($status) {
                        'pending'  => '招待中',
                        'rejected' => '拒否',
                        default    => null,
                    },
                ];
            })
            ->toArray();
    }

    public function removeParticipant(int $roomUserId): void
    {
        if (!$this->canEdit) {
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
        $this->loadParticipants();
        $this->loadDeletionLogs();
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

    private function loadDeletionLogs(): void
    {
        $this->deletionLogs = RoomUserDeletionLog::where('room_id', $this->record->id)
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
        if (!$this->canSend) {
            Notification::make()->title('送信権限がありません')->danger()->send();
            return;
        }

        if (empty(trim($this->newMessage)) && empty($this->attachments)) return;

        $attachmentService = app(AttachmentService::class);

        // 添付ファイルがある場合は1ファイル1メッセージで送信
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $error = $attachmentService->validate($file);
                if ($error) {
                    Notification::make()->title($error)->danger()->send();
                    return;
                }

                $attachment = $attachmentService->upload($file, $this->record->id);

                $message = Message::create([
                    'room_id'          => $this->record->id,
                    'user_id'          => (string) Auth::id(),
                    'user_type'        => UserType::STAFF,
                    'message'          => $this->newMessage ?? '',
                    'attachment_url'   => $attachment['attachment_url'],
                    'attachment_type'  => $attachment['attachment_type'],
                    'attachment_name'  => $attachment['attachment_name'],
                    'attachment_size'  => $attachment['attachment_size'],
                ]);

                broadcast(new MessageSent(
                    roomId:     $this->record->id,
                    userId:     (string) Auth::id(),
                    userType:   UserType::STAFF,
                    id:         $message->id,
                    message:    $this->newMessage ?? '',
                    createdAt:  now()->toISOString(),
                    userModel:  Auth::user(),
                    attachment: $attachment,
                ));
            }
            $this->attachments = [];
        } else {
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
        }

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

    public function removeAttachment(int $index): void
    {
        array_splice($this->attachments, $index, 1);
    }

    public function sendMessageWithAttachment(array $attachmentData): void
    {
        if (!$this->canSend) return;

        $message = Message::create([
            'room_id'          => $this->record->id,
            'user_id'          => (string) Auth::id(),
            'user_type'        => UserType::STAFF,
            'message'          => $this->newMessage ?? '',
            'attachment_url'   => $attachmentData['attachment_url'],
            'attachment_type'  => $attachmentData['attachment_type'],
            'attachment_name'  => $attachmentData['attachment_name'],
            'attachment_size'  => $attachmentData['attachment_size'],
        ]);

        broadcast(new MessageSent(
            roomId:     $this->record->id,
            userId:     (string) Auth::id(),
            userType:   UserType::STAFF,
            id:         $message->id,
            message:    $this->newMessage ?? '',
            createdAt:  now()->toISOString(),
            userModel:  Auth::user(),
            attachment: $attachmentData,
        ));

        $this->newMessage = '';
        $this->loadMessages();
    }
}
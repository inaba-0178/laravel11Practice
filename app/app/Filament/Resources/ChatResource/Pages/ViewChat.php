<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatResource\Pages;

use App\Filament\Resources\ChatResource;
use App\Infrastructure\Eloquent\User\Room;
use App\Infrastructure\Eloquent\User\Message;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Models\User;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ViewChat extends Page
{
    protected static string $resource = ChatResource::class;
    protected static string $view     = 'filament.pages.view-chat';

    public Room $record;
    public string $newMessage = '';
    public array $messages    = [];

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
        return [
            Action::make('back')
                ->label('一覧に戻る')
                ->color('gray')
                ->url(ListChats::getUrl()),
        ];
    }

    public function loadMessages(): void
    {
        $this->messages = Message::where('room_id', $this->record->id)
            ->with('messageReads')
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                $sender = $this->getSender($message->user_id, $message->user_type);
                return [
                    'id'        => $message->id,
                    'message'   => $message->message,
                    'user_id'   => $message->user_id,
                    'user_type' => $message->user_type,
                    'user_name' => $sender?->name ?? ($sender ? $sender->sei . $sender->mei : '不明'),
                    'created_at' => $message->created_at->format('H:i'),
                    'is_mine'   => $message->user_id === (string) Auth::id() && $message->user_type === 'staff',
                    'read_count' => $message->messageReads
                        ->filter(fn ($r) => $r->user_id !== $message->user_id)
                        ->count(),
                ];
            })
            ->toArray();

            $this->dispatch('messages-updated');
    }

    private function getSender(string $userId, string $userType): ?object
    {
        if ($userType === 'staff') {
            return User::find($userId);
        }
        return UsrUser::find($userId);
    }

    public function getParticipants(): array
    {
        return $this->record->roomUsers
            ->map(function ($roomUser) {
                $sender = $this->getSender($roomUser->user_id, $roomUser->user_type);
                return [
                    'id'        => $roomUser->user_id,
                    'user_type' => $roomUser->user_type,
                    'name'      => $roomUser->user_type === 'staff'
                        ? ($sender?->name ?? '不明')
                        : ($sender ? $sender->sei . ' ' . $sender->mei : '不明'),
                    'email'     => $sender?->email ?? '-',
                    'type_label' => $roomUser->user_type === 'staff' ? 'ディーラー' : 'ユーザー',
                ];
            })
            ->toArray();
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->newMessage))) return;

        Message::create([
            'room_id'   => $this->record->id,
            'user_id'   => (string) Auth::id(),
            'user_type' => 'staff',
            'message'   => $this->newMessage,
        ]);

        // Reverbにブロードキャスト
        broadcast(new \App\Infrastructure\Events\MessageSent(
            roomId:    $this->record->id,
            userId:    (string) Auth::id(),
            userType:  'staff',
            id:        Message::where('room_id', $this->record->id)->latest()->first()->id,
            message:   $this->newMessage,
            createdAt: now()->toISOString(),
            userModel: Auth::user(),
        ));

        $this->newMessage = '';
        $this->loadMessages();

        Notification::make()
            ->title('送信しました')
            ->success()
            ->send();
    }

    // Reverbからのイベント受信
    
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
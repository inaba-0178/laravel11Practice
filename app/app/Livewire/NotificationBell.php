<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Domain\Shared\Constants\UserType;
use App\Filament\Resources\ChatResource\Pages\ListChats;
use App\Infrastructure\Eloquent\User\Message;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount(): void
    {
        $userId = (string) auth()->id();
        if (!$userId) return;

        $this->unreadCount = Message::where('user_type', UserType::MEMBER)
            ->whereHas('room.roomUsers', fn($q) => $q
                ->where('user_id', $userId)
                ->where('user_type', UserType::STAFF)
            )
            ->whereDoesntHave('messageReads', fn($q) => $q
                ->where('user_id', $userId)
                ->where('user_type', UserType::STAFF)
            )
            ->count();
    }

    public function getUnreadMessages(): \Illuminate\Support\Collection
    {
        $userId = (string) auth()->id();
        if (!$userId) return collect();

        return Message::with('room')
            ->where('user_type', UserType::MEMBER)
            ->whereHas('room.roomUsers', fn($q) => $q
                ->where('user_id', $userId)
                ->where('user_type', UserType::STAFF)
            )
            ->whereDoesntHave('messageReads', fn($q) => $q
                ->where('user_id', $userId)
                ->where('user_type', UserType::STAFF)
            )
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getChatListUrl(): string
    {
        return ListChats::getUrl();
    }

    #[\Livewire\Attributes\On('messages-read-by-staff')]
    public function onMessagesRead(): void
    {
        $this->loadUnreadCount();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.notification-bell');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Constants\InquiryStatus;
use App\Domain\Shared\Constants\UserType;
use App\Filament\Resources\ChatResource\Pages\ListChats;
use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Infrastructure\Eloquent\User\Message;
use App\Infrastructure\Eloquent\User\StkInquiry;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $chatUnreadCount    = 0;
    public int $inquiryCount       = 0;
    public int $totalCount         = 0;

    public function mount(): void
    {
        $this->loadAllCounts();
    }

    public function loadAllCounts(): void
    {
        $this->loadChatUnreadCount();
        $this->loadInquiryCount();
        $this->totalCount = $this->chatUnreadCount + $this->inquiryCount;
    }

    private function loadChatUnreadCount(): void
    {
        $userId = (string) auth()->id();
        if (!$userId) return;

        $this->chatUnreadCount = Message::where('user_type', UserType::MEMBER)
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

    private function loadInquiryCount(): void
    {
        $user = auth()->user();
        if (!$user || !$user->dealer_id) return;

        $this->inquiryCount = StkInquiry::where('status', InquiryStatus::NEW)
            ->where('dealer_id', $user->dealer_id)
            ->count();
    }

    public function getChatListUrl(): string
    {
        return ListChats::getUrl();
    }

    public function getInquiryListUrl(): string
    {
        return ListInquiries::getUrl();
    }

    #[\Livewire\Attributes\On('messages-read-by-staff')]
    public function onMessagesRead(): void
    {
        $this->loadAllCounts();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.notification-bell');
    }
}

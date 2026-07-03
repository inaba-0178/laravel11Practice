<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Constants\CarStatus;
use App\Constants\InquiryStatus;
use App\Constants\ReservationStatus;
use App\Domain\Shared\Constants\UserType;
use App\Filament\Pages\BulkCarUploadPage;
use App\Filament\Resources\BulkCarApprovalResource\Pages\ListBulkCarApprovals;
use App\Filament\Resources\CarApprovalResource\Pages\ListCarApprovals;
use App\Filament\Resources\CarStockResource\Pages\ListCarStocks;
use App\Filament\Resources\ChatResource\Pages\ListChats;
use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Filament\Resources\ReservationResource\Pages\ListReservations;
use App\Infrastructure\Eloquent\User\Message;
use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkInquiry;
use App\Infrastructure\Eloquent\User\StkReservation;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $chatUnreadCount    = 0;
    public int $inquiryCount       = 0;
    public int $bulkCarCount           = 0;
    public int $bulkCarApprovedCount   = 0;
    public int $carApprovalCount      = 0;
    public int $bulkCarApprovalCount  = 0;
    public int $reservationCount      = 0;
    public int $totalCount            = 0;

    public function mount(): void
    {
        $this->loadAllCounts();
    }

    public function loadAllCounts(): void
    {
        $this->loadChatUnreadCount();
        $this->loadInquiryCount();
        $this->loadBulkCarCount();
        $this->loadBulkCarApprovedCount();
        $this->loadCarApprovalCount();
        $this->loadBulkCarApprovalCount();
        $this->loadReservationCount();
        $this->totalCount = $this->chatUnreadCount + $this->inquiryCount + $this->bulkCarCount + $this->bulkCarApprovedCount + $this->carApprovalCount + $this->bulkCarApprovalCount + $this->reservationCount;
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
        if (!$user) return;
        $dealerId = $user->getEffectiveDealerId();
        if (!$dealerId) return;

        $this->inquiryCount = StkInquiry::where('status', InquiryStatus::NEW)
            ->where('dealer_id', $dealerId)
            ->count();
    }

    private function loadCarApprovalCount(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['super', 'admin'])) return;

        $this->carApprovalCount = StkCar::whereNull('bulk_upload_key')
            ->where('status', CarStatus::PENDING)
            ->count();
    }

    private function loadReservationCount(): void
    {
        $user = auth()->user();
        if (!$user) return;
        $dealerId = $user->getEffectiveDealerId();
        if (!$dealerId) return;

        $this->reservationCount = StkReservation::where('dealer_id', $dealerId)
            ->where('status', ReservationStatus::PENDING->value)
            ->whereHas('schedule', fn($q) => $q->where('date', '>=', now()->toDateString()))
            ->count();
    }

    private function loadBulkCarApprovalCount(): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['super', 'admin'])) return;

        $this->bulkCarApprovalCount = StkBulkUploadBatch::where('pending_count', '>', 0)->count();
    }

    private function loadBulkCarCount(): void
    {
        $user = auth()->user();
        if (!$user) return;
        $dealerId = $user->getEffectiveDealerId();
        if (!$dealerId) return;

        // 差し戻しあり（rejected_count > 0）かつ未公開のバッチ
        $this->bulkCarCount = StkBulkUploadBatch::where('dealer_id', $dealerId)
            ->where('rejected_count', '>', 0)
            ->whereDoesntHave('cars', fn($q) => $q->where('status', CarStatus::AVAILABLE))
            ->count();
    }

    private function loadBulkCarApprovedCount(): void
    {
        $user = auth()->user();
        if (!$user) return;
        $dealerId = $user->getEffectiveDealerId();
        if (!$dealerId) return;

        // 承認済み公開前（approved_pendingの車両あり・pendingなし・未公開）
        $this->bulkCarApprovedCount = StkBulkUploadBatch::where('dealer_id', $dealerId)
            ->whereHas('cars', fn($q) => $q->where('status', CarStatus::APPROVED_PENDING))
            ->whereDoesntHave('cars', fn($q) => $q->where('status', CarStatus::PENDING))
            ->whereDoesntHave('cars', fn($q) => $q->where('status', CarStatus::AVAILABLE))
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

    public function getBulkCarUploadUrl(): string
    {
        return BulkCarUploadPage::getUrl();
    }

    public function getCarStockUrl(): string
    {
        return ListCarStocks::getUrl();
    }

    public function getCarApprovalUrl(): string
    {
        return ListCarApprovals::getUrl();
    }

    public function getBulkCarApprovalUrl(): string
    {
        return ListBulkCarApprovals::getUrl();
    }

    public function getReservationUrl(): string
    {
        return ListReservations::getUrl();
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

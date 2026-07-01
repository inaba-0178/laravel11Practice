<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Constants\Role\RoleManagement;
use App\Infrastructure\Eloquent\Opr\OprMaintenance;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class MaintenancePage extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon  = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = NavigationGroup::MST_UPDATE_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::MAINTENANCE->value;
    protected static ?string $title           = 'メンテナンスモード';
    protected static string  $view            = 'filament.pages.maintenance';
    
    public string $mode = 'manual';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, RoleManagement::MAINTENANCE_ROLES);
    }

    // ===== プロパティ =====
    public bool    $isMaintenance   = false;
    public string  $message         = '';
    public ?string $startedAt       = null;
    public ?string $estimatedEndAt  = null;

    // mount修正
    public function mount(): void
    {
        $maintenance          = OprMaintenance::getInstance();
        $this->isMaintenance  = (bool) $maintenance->is_maintenance;
        $this->message        = $maintenance->message ?? '';
        $this->startedAt      = $maintenance->started_at?->format('Y-m-d\TH:i');
        $this->estimatedEndAt = $maintenance->estimated_end_at?->format('Y-m-d\TH:i');
    }

    // enableMaintenance修正
    public function enableMaintenance(): void
    {
        $maintenance = OprMaintenance::getInstance();
        $maintenance->update([
            'is_maintenance'   => true,
            'message'          => $this->message,
            'started_at'       => $this->startedAt
                ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $this->startedAt)
                : null,
            'estimated_end_at' => $this->estimatedEndAt
                ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $this->estimatedEndAt)
                : null,
            'updated_by'       => Auth::id(),
            'updated_at'       => now(),
        ]);

        $this->isMaintenance = true;

        Notification::make()
            ->title('メンテナンスモードをONにしました')
            ->warning()
            ->send();
    }

    // disableMaintenance修正
    public function disableMaintenance(): void
    {
        $maintenance = OprMaintenance::getInstance();
        $maintenance->update([
            'is_maintenance'   => false,
            'message'          => null,
            'started_at'       => null,
            'estimated_end_at' => null,
            'updated_by'       => Auth::id(),
            'updated_at'       => now(),
        ]);

        $this->isMaintenance  = false;
        $this->message        = '';
        $this->startedAt      = null;
        $this->estimatedEndAt = null;

        Notification::make()
            ->title('メンテナンスモードをOFFにしました')
            ->success()
            ->send();
    }

    // スケジュール設定
    public function scheduleMainenance(): void
    {
        if (empty($this->startedAt) || empty($this->estimatedEndAt)) {
            Notification::make()
                ->title('開始時間と終了時間を入力してください')
                ->danger()
                ->send();
            return;
        }

        $maintenance = OprMaintenance::getInstance();
        $maintenance->update([
            'is_maintenance'   => false,
            'message'          => $this->message,
            'started_at'       => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $this->startedAt),
            'estimated_end_at' => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $this->estimatedEndAt),
            'updated_by'       => Auth::id(),
            'updated_at'       => now(),
        ]);

        Notification::make()
            ->title('メンテナンススケジュールを設定しました')
            ->success()
            ->send();
    }

    // スケジュールキャンセル
    public function cancelSchedule(): void
    {
        $maintenance = OprMaintenance::getInstance();
        $maintenance->update([
            'started_at'       => null,
            'estimated_end_at' => null,
            'message'          => null,
            'updated_by'       => Auth::id(),
            'updated_at'       => now(),
        ]);

        $this->startedAt      = null;
        $this->estimatedEndAt = null;
        $this->message        = '';

        Notification::make()
            ->title('スケジュールをキャンセルしました')
            ->warning()
            ->send();
    }
}
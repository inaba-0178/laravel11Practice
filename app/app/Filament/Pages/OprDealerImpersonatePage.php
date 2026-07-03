<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use App\Infrastructure\Eloquent\Mst\MstAreas;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Services\ImpersonationService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class OprDealerImpersonatePage extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static string  $view            = 'filament.pages.opr-dealer-impersonate';
    protected static ?string $navigationGroup = NavigationGroup::SYSTEM_GROUP->value;
    protected static ?string $title           = 'ディーラー設定';
    protected static ?int    $navigationSort  = 908;

    public ?int    $searchArea   = null;
    public ?int    $searchRegion = null;
    public string  $searchName   = '';

    public ?int    $selectedDealerId   = null;
    public ?string $selectedDealerName = null;
    public ?string $selectedDealerAddress = null;
    public bool    $showModal          = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public function getAreas(): array
    {
        return MstAreas::orderBy('sort_order')->get(['id', 'name'])->toArray();
    }

    public function getFilteredRegions(): array
    {
        if (!$this->searchArea) return [];
        return MstRegions::where('area_code', $this->searchArea)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->toArray();
    }

    public function updatedSearchArea(): void
    {
        $this->searchRegion = null;
        $this->resetPage();
    }

    public function updatedSearchRegion(): void
    {
        $this->resetPage();
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->searchArea   = null;
        $this->searchRegion = null;
        $this->searchName   = '';
        $this->resetPage();
    }

    public function getDealers(): LengthAwarePaginator
    {
        $query = StkCarDealer::query()->where('is_active', true);

        if ($this->searchRegion) {
            $query->where('region_id', $this->searchRegion);
        } elseif ($this->searchArea) {
            $regionIds = MstRegions::where('area_code', $this->searchArea)->pluck('id');
            $query->whereIn('region_id', $regionIds);
        }

        if ($this->searchName !== '') {
            $query->where('name', 'like', '%' . $this->searchName . '%');
        }

        return $query->orderBy('name')->paginate(10);
    }

    public function selectDealer(int $id, string $name, string $address): void
    {
        $this->selectedDealerId      = $id;
        $this->selectedDealerName    = $name;
        $this->selectedDealerAddress = $address;
        $this->showModal             = true;
    }

    public function confirmImpersonate(): void
    {
        abort_unless(auth()->user()?->role === RoleConstants::SUPER, 403);

        if (!$this->selectedDealerId || !$this->selectedDealerName) return;

        ImpersonationService::set($this->selectedDealerId, $this->selectedDealerName);
        $this->showModal = false;

        Notification::make()
            ->title('ディーラーを設定しました：' . $this->selectedDealerName)
            ->success()
            ->send();
    }

    public function clearImpersonate(): void
    {
        abort_unless(auth()->user()?->role === RoleConstants::SUPER, 403);

        ImpersonationService::clear();

        Notification::make()
            ->title('ディーラー設定を解除しました')
            ->success()
            ->send();
    }

    public function isImpersonating(): bool
    {
        return ImpersonationService::isActive();
    }

    public function impersonatedDealerName(): ?string
    {
        return ImpersonationService::getDealerName();
    }
}

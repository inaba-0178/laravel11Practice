<?php

declare(strict_types=1);

namespace App\Filament\Resources\AffiliatedStoreResource\Pages;

use App\Filament\Resources\AffiliatedStoreResource;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Eloquent\User\StkAffiliatedStore;
use App\Infrastructure\Eloquent\Mst\MstAreas;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Constants\AffiliatedStoreStatus;

class CreateAffiliatedStore extends Page
{
    use WithPagination;

    protected static string  $resource = AffiliatedStoreResource::class;
    protected static string  $view     = 'filament.pages.create-affiliated-store';
    protected static ?string $title    = '系列店・提携店を申請する';

    public ?int    $selectedAreaId   = null;
    public ?int    $selectedRegionId = null;
    public string  $searchName       = '';
    public array   $selectedStores   = [];
    public bool    $showConfirmModal = false;

    public function getBreadcrumbs(): array
    {
        return [
            AffiliatedStoreResource::getUrl() => '系列店・提携店管理',
            '#' => '申請',
        ];
    }

    public function updatedSelectedAreaId(): void
    {
        $this->selectedRegionId = null;
        $this->resetPage();
    }

    public function updatedSelectedRegionId(): void
    {
        $this->resetPage();
    }

    public function updatedSearchName(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function areas()
    {
        return MstAreas::orderBy('sort_order')->get();
    }

    #[Computed]
    public function regions()
    {
        if (!$this->selectedAreaId) {
            return MstRegions::orderBy('sort_order')->get();
        }
        return MstRegions::where('area_code', $this->selectedAreaId)
            ->orderBy('sort_order')
            ->get();
    }

    private function buildDealerQuery()
    {
        $myDealerId = Auth::user()->dealer_id;

        $excludeIds = StkAffiliatedStore::where(function ($q) use ($myDealerId) {
                $q->where('dealer_id', $myDealerId)
                  ->orWhere('affiliated_dealer_id', $myDealerId);
            })
            ->whereIn('status', [AffiliatedStoreStatus::PENDING, AffiliatedStoreStatus::APPROVED])
            ->get()
            ->flatMap(fn ($r) => [$r->dealer_id, $r->affiliated_dealer_id])
            ->unique()
            ->filter(fn ($id) => $id !== $myDealerId)
            ->values()
            ->toArray();

        $query = StkCarDealer::where('is_active', true)
            ->whereNull('deleted_at')
            ->where('id', '!=', $myDealerId)
            ->whereNotIn('id', $excludeIds);

        if ($this->selectedRegionId) {
            $query->where('region_id', $this->selectedRegionId);
        } elseif ($this->selectedAreaId) {
            $query->where('area_code', $this->selectedAreaId);
        }

        if (mb_strlen($this->searchName) >= 3) {
            $query->where('name', 'like', $this->searchName . '%');
        }

        return $query;
    }

    public function hasSearchCondition(): bool
    {
        return $this->selectedAreaId
            || $this->selectedRegionId
            || mb_strlen($this->searchName) >= 3;
    }

    #[Computed]
    public function dealers()
    {
        if (!$this->hasSearchCondition()) {
            return null;
        }

        return $this->buildDealerQuery()
            ->orderBy('name')
            ->simplePaginate(20);
    }

    public function toggleStore(int $dealerId, string $name, string $address, string $phone): void
    {
        if (isset($this->selectedStores[$dealerId])) {
            unset($this->selectedStores[$dealerId]);
        } else {
            $this->selectedStores[$dealerId] = [
                'dealer_id' => $dealerId,
                'name'      => $name,
                'address'   => $address,
                'phone'     => $phone,
                'type'      => 'affiliated',
            ];
        }
    }

    public function updateType(int $dealerId, string $type): void
    {
        if (isset($this->selectedStores[$dealerId])) {
            $this->selectedStores[$dealerId]['type'] = $type;
        }
    }

    public function removeStore(int $dealerId): void
    {
        unset($this->selectedStores[$dealerId]);
    }

    public function confirmApply(): void
    {
        if (empty($this->selectedStores)) {
            Notification::make()
                ->title('店舗を選択してください')
                ->warning()
                ->send();
            return;
        }
        $this->showConfirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
    }

    public function apply(): void
    {
        $user  = Auth::user();
        $count = count($this->selectedStores);

        foreach ($this->selectedStores as $store) {
            StkAffiliatedStore::create([
                'dealer_id'            => $user->dealer_id,
                'affiliated_dealer_id' => $store['dealer_id'],
                'type'                 => $store['type'],
                'status'               => AffiliatedStoreStatus::PENDING,
                'requested_by'         => $user->id,
                'requested_at'         => now(),
            ]);
        }

        $this->showConfirmModal = false;
        $this->selectedStores  = [];

        Notification::make()
            ->title($count . '件申請しました')
            ->success()
            ->send();

        $this->redirect(AffiliatedStoreResource::getUrl('index'));
    }
}
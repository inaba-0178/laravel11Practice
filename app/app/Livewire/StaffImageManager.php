<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Infrastructure\Eloquent\User\StkDealerStaff;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class StaffImageManager extends Component
{
    public int     $staffId;
    public string  $staffName  = '';
    public ?string $imageUrl   = null;

    // 確認モーダル
    public bool $showDeleteConfirm = false;

    public function mount(int $staffId): void
    {
        $this->staffId = $staffId;
        $this->loadStaffInfo();
    }

    private function loadStaffInfo(): void
    {
        $staff           = StkDealerStaff::find($this->staffId);
        $this->staffName = $staff?->name ?? '';
        $this->imageUrl  = $staff?->image_path
            ? Storage::disk('s3')->url($staff->image_path)
            : null;
    }

    // ===== 画像削除 =====
    public function confirmDelete(): void
    {
        $this->showDeleteConfirm = true;
    }

    public function deleteImage(): void
    {
        $staff = StkDealerStaff::find($this->staffId);
        if ($staff?->image_path) {
            Storage::disk('s3')->delete($staff->image_path);
            $staff->update(['image_path' => null]);
        }

        $this->showDeleteConfirm = false;
        $this->loadStaffInfo();
        $this->dispatch('notify', type: 'success', message: '画像を削除しました');
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
    }

    #[\Livewire\Attributes\On('staff-image-uploaded')]
    public function refreshImage(): void
    {
        $this->loadStaffInfo();
        $this->dispatch('notify', type: 'success', message: '画像をアップロードしました');
    }

    public function render()
    {
        return view('filament.livewire.staff-image-manager');
    }
}
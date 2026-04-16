<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Infrastructure\Eloquent\User\StkDealerStaff;
use App\Infrastructure\Eloquent\User\StkDealerContent;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class AssetImageManager extends Component
{
    public string  $type;
    public int     $recordId;
    public string  $label     = '';
    public ?string $imageUrl  = null;

    public bool $showDeleteConfirm = false;

    public function mount(string $type, int $recordId): void
    {
        $this->type     = $type;
        $this->recordId = $recordId;
        $this->loadInfo();
    }

    private function loadInfo(): void
    {
        $record = $this->findRecord();
        if (!$record) return;

        $this->label    = $record->name ?? $record->title ?? '';
        $this->imageUrl = $record->image_path
            ? Storage::disk('s3')->url($record->image_path)
            : null;
    }

    private function findRecord(): ?object
    {
        return match($this->type) {
            'staff'   => StkDealerStaff::whereNull('deleted_at')->find($this->recordId),
            'content' => StkDealerContent::whereNull('deleted_at')->find($this->recordId),
            default   => null,
        };
    }

    public function confirmDelete(): void
    {
        $this->showDeleteConfirm = true;
    }

    public function deleteImage(): void
    {
        $record = $this->findRecord();
        if ($record?->image_path) {
            Storage::disk('s3')->delete($record->image_path);
            $record->update(['image_path' => null]);
        }

        $this->showDeleteConfirm = false;
        $this->loadInfo();
        $this->dispatch('notify', type: 'success', message: '画像を削除しました');
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
    }

    #[\Livewire\Attributes\On('asset-image-uploaded')]
    public function refreshImage(): void
    {
        $this->loadInfo();
        $this->dispatch('notify', type: 'success', message: '画像をアップロードしました');
    }

    public function render()
    {
        return view('filament.livewire.asset-image-manager');
    }
}
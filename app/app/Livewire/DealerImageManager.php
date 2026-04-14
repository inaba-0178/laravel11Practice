<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Eloquent\User\StkDealerImage;
use App\Infrastructure\Eloquent\User\StkDealerImageText;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DealerImageManager extends Component
{
    public int    $dealerId;
    public string $dealerName = '';

    // 登録済み画像
    public array $images = [];

    // チェックボックス選択
    public array $selectedIds = [];

    // 拡大表示
    public ?string $previewUrl = null;

    // 確認モーダル
    public bool $showDeleteConfirm    = false;
    public bool $showDeleteAllConfirm = false;

    // キャプション編集
    public ?int    $editingCaptionImageId = null;
    public string  $editingCaption        = '';

    public function mount(int $dealerId): void
    {
        $this->dealerId = $dealerId;
        $this->loadDealerInfo();
        $this->loadImages();
    }

    private function loadDealerInfo(): void
    {
        $dealer           = StkCarDealer::find($this->dealerId);
        $this->dealerName = $dealer?->name ?? '';
    }

    private function loadImages(): void
    {
        $this->images = StkDealerImage::where('dealer_id', $this->dealerId)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($img) => [
                'id'      => $img->id,
                'url'     => Storage::disk('s3')->url($img->image_path),
                'alt'     => $img->alt_text ?? '',
                'is_main' => $img->is_main,
                'caption' => $img->text?->caption ?? '',
            ])
            ->toArray();
    }

    // ===== 画像並び替え =====
    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            StkDealerImage::where('id', $id)
                ->where('dealer_id', $this->dealerId)
                ->update(['sort_order' => $order + 1]);
        }

        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: '並び順を保存しました');
    }

    // ===== メイン画像設定 =====
    public function setMain(int $imageId): void
    {
        StkDealerImage::where('dealer_id', $this->dealerId)->update(['is_main' => false]);
        StkDealerImage::where('id', $imageId)->update(['is_main' => true]);

        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: 'メイン画像を設定しました');
    }

    // ===== キャプション編集 =====
    public function startEditCaption(int $imageId, string $currentCaption): void
    {
        $this->editingCaptionImageId = $imageId;
        $this->editingCaption        = $currentCaption;
    }

    public function saveCaption(): void
    {
        if (!$this->editingCaptionImageId) return;

        $image = StkDealerImage::where('id', $this->editingCaptionImageId)
            ->where('dealer_id', $this->dealerId)
            ->first();

        if (!$image) return;

        StkDealerImageText::updateOrCreate(
            ['image_id' => $image->id],
            ['caption'  => $this->editingCaption],
        );

        $this->editingCaptionImageId = null;
        $this->editingCaption        = '';

        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: 'キャプションを保存しました');
    }

    public function cancelEditCaption(): void
    {
        $this->editingCaptionImageId = null;
        $this->editingCaption        = '';
    }

    // ===== 拡大表示 =====
    public function preview(string $url): void
    {
        $this->previewUrl = $url;
    }

    public function closePreview(): void
    {
        $this->previewUrl = null;
    }

    // ===== 選択削除 =====
    public function confirmDelete(): void
    {
        if (empty($this->selectedIds)) return;
        $this->showDeleteConfirm = true;
    }

    public function deleteSelected(): void
    {
        $images = StkDealerImage::whereIn('id', $this->selectedIds)
            ->where('dealer_id', $this->dealerId)
            ->get();

        foreach ($images as $image) {
            Storage::disk('s3')->delete($image->image_path);
            $image->delete();
        }

        $this->selectedIds       = [];
        $this->showDeleteConfirm = false;
        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: '選択した画像を削除しました');
    }

    // ===== 全件削除 =====
    public function confirmDeleteAll(): void
    {
        $this->showDeleteAllConfirm = true;
    }

    public function deleteAll(): void
    {
        $images = StkDealerImage::where('dealer_id', $this->dealerId)
            ->whereNull('deleted_at')
            ->get();

        foreach ($images as $image) {
            Storage::disk('s3')->delete($image->image_path);
            $image->delete();
        }

        $this->selectedIds          = [];
        $this->showDeleteAllConfirm = false;
        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: '全ての画像を削除しました');
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm    = false;
        $this->showDeleteAllConfirm = false;
    }

    #[\Livewire\Attributes\On('dealer-images-uploaded')]
    public function refreshImages(): void
    {
        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: '画像をアップロードしました');
    }

    public function render()
    {
        return view('filament.livewire.dealer-image-manager');
    }
}
<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Constants\ImageType;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarImages;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstVehicles;

class CarImageManager extends Component
{
    use WithFileUploads;

    // 車両情報
    public int    $carId;
    public string $carLabel    = '';
    public string $carSubLabel = '';

    // アップロードフォーム
    public array  $uploadFiles  = [];
    public string $imageType    = ImageType::EXTERIOR;

    // 登録済み画像
    public array $images        = [];

    // チェックボックス選択
    public array $selectedIds   = [];

    // 拡大表示
    public ?string $previewUrl  = null;

    // 確認モーダル
    public bool $showDeleteConfirm    = false;
    public bool $showDeleteAllConfirm = false;

    public function mount(int $carId): void
    {
        $this->carId = $carId;
        $this->loadCarInfo();
        $this->loadImages();
    }

    private function loadCarInfo(): void
    {
        $car = StkCar::find($this->carId);
        if (!$car) return;

        $series  = MstCarSeries::find($car->series_id);
        $vehicle = MstVehicles::find($car->vehicle_id);

        $this->carLabel = implode(' ', array_filter([
            $series?->series_name,
            $vehicle?->name,
            $car->model_year ? "{$car->model_year}年式" : null,
            "#{$car->id}",
        ]));

        $this->carSubLabel = implode('　', array_filter([
            '修復歴：' . match($car->repair_history) {
                'none'    => 'なし',
                'minor'   => '軽微あり',
                'major'   => 'あり',
                'unknown' => '不明',
                default   => '不明',
            },
            number_format($car->mileage) . 'km',
        ]));
    }

    private function loadImages(): void
    {
        $this->images = StkCarImages::where('car_id', $this->carId)
            ->orderBy('display_order')
            ->get()
            ->map(fn ($img) => [
                'id'       => $img->id,
                'url'      => Storage::disk('s3')->url($img->image_url),
                'type'     => $img->image_type,
                'type_label' => ImageType::LABELS[$img->image_type] ?? $img->image_type,
                'is_main'  => $img->is_main,
            ])
            ->toArray();
    }

    // ===== アップロード処理 =====
    // CarImageManager.php は単一ファイルのままでOK
    public $uploadFile = null;

    public function upload(): void
    {
        $this->validate([
            'uploadFile' => 'required|image|max:10240',
            'imageType'  => 'required|in:exterior,interior,engine,other',
        ]);

        $maxOrder = StkCarImages::where('car_id', $this->carId)->max('display_order') ?? 0;

        $path = $this->uploadFile->store("car-images/{$this->carId}", 's3');

        StkCarImages::create([
            'car_id'        => $this->carId,
            'image_url'     => $path,
            'image_type'    => $this->imageType,
            'display_order' => ++$maxOrder,
            'is_main'       => 0,
        ]);

        $this->uploadFile = null;
        $this->loadImages();
        $this->selectedIds = [];

        $this->dispatch('notify', type: 'success', message: '画像をアップロードしました');
    }

    // ===== メイン画像設定 =====
    public function setMain(int $imageId): void
    {
        StkCarImages::where('car_id', $this->carId)->update(['is_main' => 0]);
        StkCarImages::where('id', $imageId)->update(['is_main' => 1]);

        $mainImage = StkCarImages::find($imageId);
        if ($mainImage) {
            StkCar::where('id', $this->carId)->update(['main_image_url' => $mainImage->image_url]);
        }

        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: 'メイン画像を設定しました');
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

    // ===== 選択削除確認 =====
    public function confirmDelete(): void
    {
         \Log::info('confirmDelete called', ['selectedIds' => $this->selectedIds]);
        if (empty($this->selectedIds)) return;
        $this->showDeleteConfirm = true;
    }

    public function deleteSelected(): void
    {
        $images = StkCarImages::whereIn('id', $this->selectedIds)
            ->where('car_id', $this->carId)
            ->get();

        foreach ($images as $image) {
            Storage::disk('s3')->delete($image->image_url);
            $image->delete();
        }

        $this->selectedIds       = [];
        $this->showDeleteConfirm = false;
        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: '選択した画像を削除しました');
    }

    // ===== 全件削除確認 =====
    public function confirmDeleteAll(): void
    {
        $this->showDeleteAllConfirm = true;
    }

    public function deleteAll(): void
    {
        $images = StkCarImages::where('car_id', $this->carId)->get();

        foreach ($images as $image) {
            Storage::disk('s3')->delete($image->image_url);
            $image->delete();
        }

        StkCar::where('id', $this->carId)->update(['main_image_url' => null]);

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

    public function getImageTypeOptions(): array
    {
        return ImageType::LABELS;
    }

    public function render()
    {
        return view('filament.livewire.car-image-manager');
    }

    #[\Livewire\Attributes\On('images-uploaded')]
    public function refreshImages(): void
    {
        \Log::info('refreshImages called');
        $this->loadImages();
        $this->dispatch('notify', type: 'success', message: '画像をアップロードしました');
    }
}
<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Infrastructure\Eloquent\User\StkDealerImage;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DealerImageCarousel extends Component
{
    public int   $dealerId;
    public array $images      = [];
    public int   $currentIndex = 0;

    public function mount(int $dealerId): void
    {
        $this->dealerId = $dealerId;
        $this->loadImages();
    }

    private function loadImages(): void
    {
        $this->images = StkDealerImage::where('dealer_id', $this->dealerId)
            ->whereNull('deleted_at')
            ->orderBy('is_main', 'desc')
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

    public function setIndex(int $index): void
    {
        $this->currentIndex = $index;
    }

    public function prev(): void
    {
        $this->currentIndex = $this->currentIndex > 0
            ? $this->currentIndex - 1
            : count($this->images) - 1;
    }

    public function next(): void
    {
        $this->currentIndex = $this->currentIndex < count($this->images) - 1
            ? $this->currentIndex + 1
            : 0;
    }

    public function render()
    {
        return view('filament.livewire.dealer-image-carousel');
    }
}
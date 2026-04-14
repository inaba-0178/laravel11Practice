<x-filament-panels::page>
    {{ $this->infoList }}

    <x-filament::section>
        <x-slot name="heading">店舗画像</x-slot>
        @livewire('dealer-image-carousel', ['dealerId' => $this->record->id])
    </x-filament::section>
</x-filament-panels::page>
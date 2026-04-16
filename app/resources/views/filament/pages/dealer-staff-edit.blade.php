<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
        <x-filament::button wire:click="save" color="primary">
            保存する
        </x-filament::button>
    </div>

    @livewire('staff-image-manager', ['staffId' => $this->record->id])
</x-filament-panels::page>
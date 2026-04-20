<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
        <x-filament::button wire:click="save" color="primary">
            パスワードを変更する
        </x-filament::button>
    </div>
</x-filament-panels::page>
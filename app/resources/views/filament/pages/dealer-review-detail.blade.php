<x-filament-panels::page>
    {{ $this->infoList }}

    <x-filament::section>
        <x-slot name="heading">返信一覧</x-slot>
        {{ $this->table }}
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">返信する</x-slot>
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>
        <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
            <x-filament::button wire:click="submitReply" color="primary">
                返信する
            </x-filament::button>
        </div>
    </x-filament::section>

</x-filament-panels::page>
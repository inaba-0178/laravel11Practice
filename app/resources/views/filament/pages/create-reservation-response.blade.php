<x-filament-panels::page>
    <x-filament::section>{{ $this->form }}</x-filament::section>

    <div class="flex gap-4 mt-4">
        @if(!$this->record->response)
            <x-filament::button color="gray" tag="a" :href="$this->getResource()::getUrl('view', ['record' => $this->record->id])">
                キャンセル
            </x-filament::button>
        @endif
        
        <x-filament::button wire:click="validateAndOpenModal" color="primary">
            {{ $this->record->response ? '更新する' : '保存する' }}
        </x-filament::button>
    </div>

    {{-- 確認モーダル --}}
    <x-filament::modal id="confirm-save-modal">
        <x-slot name="heading">本当に保存しますか？</x-slot>
        <x-slot name="footerActions">
            <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'confirm-save-modal' })">
                キャンセル
            </x-filament::button>
            <x-filament::button wire:click="save" color="primary">
                保存する
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    {{-- 成功モーダル --}}
    <x-filament::modal id="success-modal">
        <x-slot name="heading">保存完了</x-slot>
        <x-slot name="description">対応内容を保存しました。</x-slot>
        <x-slot name="footerActions">
            <x-filament::button color="primary" x-on:click="
                $dispatch('close-modal', { id: 'success-modal' });
                $dispatch('close-modal', { id: 'confirm-save-modal' });
            ">
                閉じる
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>

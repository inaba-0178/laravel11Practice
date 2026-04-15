<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    @livewire('dealer-image-manager', ['dealerId' => $this->record->id])

    {{-- 画像未アップロード警告をモーダルに注入 --}}
    <div
        x-data="{}"
        x-init="
            const observer = new MutationObserver(() => {
                const warning = document.getElementById('upload-warning');
                if (warning && window.dealerImageFileCount > 0) {
                    warning.style.display = 'block';
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        "
    ></div>
</x-filament-panels::page>
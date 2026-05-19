<?php

declare(strict_types=1);

namespace App\Filament\Resources\MstVersionResource\Pages;

use App\Filament\Resources\MstVersionResource;
use Filament\Resources\Pages\ListRecords;

class ListMstVersions extends ListRecords
{
    protected static string $resource = MstVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        $activeVersion = \App\Infrastructure\Eloquent\Mst\MstVersion::where('status', 'active')->latest()->first();

        return [];
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        $activeVersion = \App\Infrastructure\Eloquent\Mst\MstVersion::where('status', 'active')->latest()->first();

        return view('filament.pages.mst-version-header', compact('activeVersion'));
    }
}
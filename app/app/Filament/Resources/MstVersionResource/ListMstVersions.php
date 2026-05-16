<?php

declare(strict_types=1);

namespace App\Filament\Resources\MstVersionResource\Pages;

use App\Filament\Resources\MstVersionResource;
use Filament\Resources\Pages\ListRecords;

class ListMstVersions extends ListRecords
{
    protected static string $resource = MstVersionResource::class;
}
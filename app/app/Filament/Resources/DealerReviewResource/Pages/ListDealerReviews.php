<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerReviewResource\Pages;

use App\Filament\Resources\DealerReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListDealerReviews extends ListRecords
{
    protected static string $resource = DealerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
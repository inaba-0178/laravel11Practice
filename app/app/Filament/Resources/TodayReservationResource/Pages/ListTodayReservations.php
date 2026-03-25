<?php
namespace App\Filament\Resources\TodayReservationResource\Pages;

use App\Filament\Resources\TodayReservationResource;
use Filament\Resources\Pages\ListRecords;

class ListTodayReservations extends ListRecords
{
    protected static string $resource = TodayReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return '本日の予約（' . now()->format('Y/m/d') . '）';
    }
}
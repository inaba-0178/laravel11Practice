<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerLoanResource\Pages;

use App\Constants\LoanMonths;
use App\Filament\Resources\DealerLoanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDealerLoan extends CreateRecord
{
    protected static string $resource = DealerLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dealer_id']      = Auth::user()->dealer_id;
        $data['is_active']      = 1;
        $data['months_options'] = $this->generateMonthsOptions(
            (int) $data['min_months'],
            (int) $data['max_months']
        );
        return $data;
    }

    private function generateMonthsOptions(int $min, int $max): array
    {
        return collect(LoanMonths::OPTIONS)
            ->keys()
            ->filter(fn ($month) => $month >= $min && $month <= $max)
            ->values()
            ->toArray();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
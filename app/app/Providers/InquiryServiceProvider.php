<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\Inquiry\CreateInquiryUseCase;
use App\Infrastructure\Repositories\Inquiry\EloquentInquiryRepository;
use Illuminate\Support\ServiceProvider;

class InquiryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CreateInquiryUseCase::class, function () {
            return new CreateInquiryUseCase(
                new EloquentInquiryRepository(),
            );
        });
    }

    public function boot(): void {}
}
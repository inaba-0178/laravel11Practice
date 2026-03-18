<?php
 
namespace App\Providers;
 
use App\Domain\LoanDownOption\Repositories\LoanDownOptionRepositoryInterface;
use App\Infrastructure\Repositories\LoanDownOption\EloquentLoanDownOptionRepository;
use Illuminate\Support\ServiceProvider;
 
class LoanDownOptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LoanDownOptionRepositoryInterface::class,
            EloquentLoanDownOptionRepository::class,
        );
    }
}
 
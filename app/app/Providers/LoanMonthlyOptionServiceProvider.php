<?php
 
namespace App\Providers;
 
use App\Domain\LoanMonthlyOption\Repositories\LoanMonthlyOptionRepositoryInterface;
use App\Infrastructure\Repositories\LoanMonthlyOption\EloquentLoanMonthlyOptionRepository;
use Illuminate\Support\ServiceProvider;
 
class LoanMonthlyOptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LoanMonthlyOptionRepositoryInterface::class,
            EloquentLoanMonthlyOptionRepository::class,
        );
    }
}
 
<?php

namespace App\Providers;

use App\Infrastructure\Eloquent\Mst\MstBasicOptions;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstCountries;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarDetail;
use App\Infrastructure\Eloquent\User\StkCarImages;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Infrastructure\Observers\CarObserver;
use App\Infrastructure\Observers\MemberObserver;
use App\Infrastructure\Observers\MstObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        StkCar::observe(CarObserver::class);
        StkCarDetail::observe(CarObserver::class);
        StkCarImages::observe(CarObserver::class);

        MstCountries::observe(MstObserver::class);
        MstBasicOptions::observe(MstObserver::class);
        MstBodyTypes::observe(MstObserver::class);
        MstManufacturers::observe(MstObserver::class);
        MstCarSeries::observe(MstObserver::class);

        UsrUser::observe(MemberObserver::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}

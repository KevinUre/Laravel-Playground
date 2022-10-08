<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NumberService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NumberService::class, function () {
            return new NumberService(rand());
        });

        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NumberService;
use OpenTracing\GlobalTracer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $tracer = GlobalTracer::get();
        $scope = $tracer->startActiveSpan('Backend Call');
        $this->app->singleton(NumberService::class, function () {
            return new NumberService(rand());
        });
        $this->app->instance('context.tracer.globalSpan',$scope);
        app()->terminating(function () {
            app('context.tracer.globalSpan')->close();
            $tracer = GlobalTracer::get();
            $tracer->flush();
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

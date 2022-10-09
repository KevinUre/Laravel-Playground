<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NumberService;
use OpenTracing\GlobalTracer;
use OpenTracing\Formats;

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
        $spanContext = GlobalTracer::get()->extract(
            Formats\HTTP_HEADERS,
            getallheaders()
        );
        //$scope = $tracer->startActiveSpan('Backend Call');
        // $scope = $tracer->startActiveSpan('getDate', ['child_of' => $spanContext]);
        if (is_null($spanContext)) {
            $scope = $tracer->startActiveSpan('Backend Call');
            $this->app->instance('context.tracer.globalSpan',$scope);
        } else {
            $scope = $tracer->startActiveSpan('Backend Call', ['child_of' => $spanContext]);
            $this->app->instance('context.tracer.globalSpan',$scope);
        }
        // $this->app->instance('context.tracer.globalSpan',$scope);
        app()->terminating(function () {
            app('context.tracer.globalSpan')->close();
            $tracer = GlobalTracer::get();
            $tracer->flush();
        });
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

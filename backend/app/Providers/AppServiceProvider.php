<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NumberService;
use OpenTracing\GlobalTracer;
use OpenTracing\Formats;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Event::listen(RequestHandled::class, function (RequestHandled $e) {
            app('context.tracer.globalSpan')->getSpan()->setTag('user_id', auth()->user()->id ?? "-");
            app('context.tracer.globalSpan')->getSpan()->setTag('request_host', $e->request->getHost());
            app('context.tracer.globalSpan')->getSpan()->setTag('request_path', $path = $e->request->path());
            app('context.tracer.globalSpan')->getSpan()->setTag('request_method', $e->request->method());
            app('context.tracer.globalSpan')->getSpan()->setTag('response_status', $e->response->getStatusCode());
            if ($e->response->getStatusCode() == 500) {
                app('context.tracer.globalSpan')->getSpan()->setTag('error', true);
            } else {
                app('context.tracer.globalSpan')->getSpan()->setTag('error', false);
            }
            // app('context.tracer.globalSpan')->getSpan()->setTag('error', !$e->response->isSuccessful(),);
        });
        Event::listen(MessageLogged::class, function (MessageLogged $e) {
            $tracer = GlobalTracer::get();
            $span = $tracer->getActiveSpan();
            $span->log((array) $e);
        });
        DB::listen(function ($query) {
            Log::debug("[DB Query] {$query->connection->getName()}", [
                'query' => str_replace('"', "'", $query->sql),
                'time' => $query->time.'ms',
            ]);
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

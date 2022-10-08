<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

$Profiling_Timings = [];
$Profiling_Names = [];

function Profile_Event(string $message): void
{
  global $Profiling_Timings, $Profiling_Names;
  $Profiling_Timings[] = microtime(true);
  $Profiling_Names[] = $message;
}

function Profile_Print(): void
{
  global $Profiling_Timings, $Profiling_Names;
  $size = count($Profiling_Timings);
  for ($i = 0; $i < $size - 1; $i++) {
    $difference = $Profiling_Timings[$i + 1] - $Profiling_Timings[$i];
    Log::info("{$Profiling_Names[$i]}: {$difference} seconds");
  }
  $overall = $Profiling_Timings[$size - 1] - $Profiling_Timings[0];
  Log::info("Overall Request Time: {$overall} seconds");
}


define('LARAVEL_START', microtime(true));
Profile_Event("App Start");

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
  require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

Profile_Event("Vendor Autoload");
require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

Profile_Event("App Bootstrap");
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Jaeger\Config;
use OpenTracing\GlobalTracer;

$config = new Config(
  [
    'sampler' => [
      'type' => Jaeger\SAMPLER_TYPE_CONST,
      'param' => true,
    ],
    'logging' => true,
    "local_agent" => [
      "reporting_host" => "jaeger"//,
      //        You can override port by setting local_agent.reporting_port value   
      //"reporting_port" => 6832
    ],
    //     Different ways to send data to Jaeger. Config::ZIPKIN_OVER_COMPACT - default):
    'dispatch_mode' => Config::JAEGER_OVER_BINARY_UDP,
  ],
  'Backend'
);
$config->initializeTracer();
$tracer = GlobalTracer::get();

Profile_Event("Create Kernel");
$kernel = $app->make(Kernel::class);

Profile_Event("Handle Request");
$response = $kernel->handle(
  $request = Request::capture()
)->send();

Profile_Event("Terminal Kernel");
$kernel->terminate($request, $response);

Profile_Event("App Terminate");
Profile_Print();

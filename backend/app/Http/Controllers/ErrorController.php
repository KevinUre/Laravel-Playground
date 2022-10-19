<?php

namespace App\Http\Controllers;

use Error;
use Illuminate\Http\Request;
use OpenTracing\GlobalTracer;

class ErrorController extends Controller
{
    public function fiveHundred() {
        return response()->json(null, 500);
    }
    public function fourOhFour() {
        return response()->json(null, 404);
    }
    public function fourHundred() {
        return response()->json(null, 400);
    }
    public function error() {
        throw new Error('Whoops');
    }

    public function headers() {
        $tracer = GlobalTracer::get();
        $scope = $tracer->startActiveSpan('Get Headers Request');
        $headers = getallheaders();
        $scope->close();
        return response()->json($headers, 200);
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Models\Article;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ErrorController;
use App\Services\NumberService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/apiregister', [AuthController::class, 'register']);
Route::post('/apilogin', [AuthController::class, 'login']);
// Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('articles', [ArticleController::class, 'index']);
Route::middleware('auth:sanctum')->get('articles/{article}', [ArticleController::class, 'show']);
Route::middleware('auth:sanctum')->post('articles', [ArticleController::class, 'store']);
Route::middleware('auth:sanctum')->put('articles/{article}', [ArticleController::class, 'update']);
Route::middleware('auth:sanctum')->delete('articles/{article}', [ArticleController::class, 'delete']);

Route::get('/fiveHundred', [ErrorController::class, 'fiveHundred']);
Route::get('/fourOhFour', [ErrorController::class, 'fourOhFour']);
Route::get('/fourHundred', [ErrorController::class, 'fourHundred']);
Route::get('/error', [ErrorController::class, 'error']);

Route::get('/number', function() {
    return app(NumberService::class)->GetNumber();
});
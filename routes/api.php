<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Models\Article;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('articles', [ArticleController::class, 'index']);
Route::middleware('auth:sanctum')->get('articles/{article}', [ArticleController::class, 'show']);
Route::middleware('auth:sanctum')->post('articles', [ArticleController::class, 'store']);
Route::middleware('auth:sanctum')->put('articles/{article}', [ArticleController::class, 'update']);
Route::middleware('auth:sanctum')->delete('articles/{article}', [ArticleController::class, 'delete']);

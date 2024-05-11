<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;

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

Route::apiResource('game', GameController::class);

// Route::prefix('game')->group(function () {
//     Route::post('/', GameController::class . '@createGame');
//     Route::post('/{gameId}/attempt', 'Api\GameController@makeAttempt');
//     Route::delete('/{gameId}', 'Api\GameController@deleteGame');
//     Route::get('/{gameId}/attempt/{attemptNumber}', 'Api\GameController@getAttemptResponse');
// });

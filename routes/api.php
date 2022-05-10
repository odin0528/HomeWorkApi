<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{AdminController, VoteController};

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

Route::group([
    'prefix' => 'admin'
], function () {
    Route::post('login', [AdminController::class, 'login']);

    Route::middleware('verifyJWT')->group(function () {
        Route::post('votes', [VoteController::class, 'index']);
        Route::get('votes/{id}', [VoteController::class, 'fetch']);
        Route::post('votes/save', [VoteController::class, 'save']);
        Route::post('votes/status', [VoteController::class, 'updateStatus']);
        Route::post('votes/{id}/candidate', [VoteController::class, 'getCandidate']);
        Route::post('votes/logs/{id}', [VoteController::class, 'fetchLogs']);
    });
});

Route::get('votes', [VoteController::class, 'fetchAll']);
Route::get('votes/{id}', [VoteController::class, 'fetchCandidate']);
Route::post('votes', [VoteController::class, 'vote']);
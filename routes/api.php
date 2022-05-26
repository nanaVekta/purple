<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ItemsController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BidsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
    });

    Route::prefix('users')->group(function () {
        Route::put('/update', [UserController::class, 'updateProfile']);
    });

    Route::prefix('items')->group(function() {
        Route::post('/create', [ItemsController::class, 'createItem']);
    });

    Route::prefix('bids')->group(function() {
        Route::post('/create', [BidsController::class, 'createBid']);
        Route::post('/make-bid', [BidsController::class, 'makeBid']);
    });
});

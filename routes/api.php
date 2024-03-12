<?php

use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\Authentication\RegisterController;
use App\Http\Controllers\ExpirationDateController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 */
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [RegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('login', 'store')->middleware('guest');
    Route::post('logout', 'destroy')->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::patch('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    Route::post('expiration_dates', [ExpirationDateController::class, 'store']);
    Route::patch('expiration_dates/{expirationDate}', [ExpirationDateController::class, 'update']);
});

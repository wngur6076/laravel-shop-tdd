<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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

Route::prefix('auth')->group(function () {
    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });
    Route::group(['middleware' => 'auth:api'], function () {
        Route::delete('/login', [LoginController::class, 'destroy'])->name('login.destroy');
    });
});

<?php

use Illuminate\Http\Request;
use oval\Http\Controllers\Api\Lti1p1;
use oval\Http\Controllers\Api\Lti1p3;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');



Route::prefix('lti')->group(function () {
    Route::prefix('1.1')->group(function () {
        Route::post('/launch', [Lti1p1\ToolController::class, 'launch'])->name('lti1p1.launch');
    });

    Route::prefix('1.3')->group(function () {
        Route::get('/jwks', [Lti1p3\ToolController::class, 'jwks'])->name('lti1p3.jwks');
        Route::get('/login', [Lti1p3\ToolController::class, 'login'])->name('lti1p3.login');
        Route::post('/launch', [Lti1p3\ToolController::class, 'launch'])->name('lti1p3.launch');
    });
});

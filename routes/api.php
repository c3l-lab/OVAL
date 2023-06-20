<?php

use Illuminate\Http\Request;
use oval\Http\Controllers\Api\Lti\ToolController;

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
    Route::get('/jwks', [ToolController::class, 'jwks'])->name('lti.jwks');
    Route::get('/login', [ToolController::class, 'login'])->name('lti.login');
    Route::post('/launch', [ToolController::class, 'launch'])->name('lti.launch');
});

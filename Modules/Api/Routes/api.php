<?php

use Illuminate\Http\Request;

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
Route::prefix('v1')->group(function () {

    Route::post('logind', 'v1\AuthController@login');
    Route::post('registers', 'v1\AuthController@register');
    Route::middleware('auth:api')->post('/logoutd', 'v1\AuthController@logout');

});

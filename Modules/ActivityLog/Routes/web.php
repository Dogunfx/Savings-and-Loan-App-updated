<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::prefix('activity_log')->group(function () {
    Route::get('/', 'ActivityLogController@index');
    Route::get('get_activity_logs', 'ActivityLogController@get_activity_logs');
    Route::get('{id}/show', 'ActivityLogController@show');
});

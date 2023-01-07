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
Route::get('/', 'DashboardController@index');
Route::prefix('dashboard')->group(function () {
    Route::get('/', 'DashboardController@index');
    Route::post('store_widget', 'DashboardController@store_widget');
    Route::post('update_widget_positions', 'DashboardController@update_widget_positions');
    Route::post('remove_widget', 'DashboardController@remove_widget');

});

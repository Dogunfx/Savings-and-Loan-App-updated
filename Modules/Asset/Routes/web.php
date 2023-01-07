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

Route::prefix('asset')->group(function () {
    Route::get('/', 'AssetController@index');
    Route::get('get_assets', 'AssetController@get_assets');
    Route::get('create', 'AssetController@create');
    Route::post('store', 'AssetController@store');
    Route::get('{id}/show', 'AssetController@show');
    Route::get('{id}/edit', 'AssetController@edit');
    Route::post('{id}/update', 'AssetController@update');
    Route::get('{id}/destroy', 'AssetController@destroy');
    //asset types
    Route::prefix('type')->group(function () {
        Route::get('/', 'AssetTypeController@index');
        Route::get('get_asset_types', 'AssetTypeController@get_asset_types');
        Route::get('create', 'AssetTypeController@create');
        Route::post('store', 'AssetTypeController@store');
        Route::get('{id}/show', 'AssetTypeController@show');
        Route::get('{id}/edit', 'AssetTypeController@edit');
        Route::post('{id}/update', 'AssetTypeController@update');
        Route::get('{id}/destroy', 'AssetTypeController@destroy');
    });
});

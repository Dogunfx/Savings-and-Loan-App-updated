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
    Route::prefix('custom_field')->group(function () {
        Route::get('/', 'Api\v1\CustomFieldController@index');
        Route::get('get_categories', 'Api\v1\CustomFieldController@get_categories');
        Route::get('get_types', 'Api\v1\CustomFieldController@get_types');
        Route::get('create', 'Api\v1\CustomFieldController@create');
        Route::post('store', 'Api\v1\CustomFieldController@store');
        Route::get('{id}/edit', 'Api\v1\CustomFieldController@edit');
        Route::post('{id}/update', 'Api\v1\CustomFieldController@update');
        Route::get('{id}/destroy', 'Api\v1\CustomFieldController@destroy');
    });
});

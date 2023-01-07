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

Route::prefix('custom_field')->group(function () {
    Route::get('/', 'CustomFieldController@index');
    Route::get('get_custom_fields', 'CustomFieldController@get_custom_fields');
    Route::get('create', 'CustomFieldController@create');
    Route::post('store', 'CustomFieldController@store');
    Route::get('{id}/edit', 'CustomFieldController@edit');
    Route::post('{id}/update', 'CustomFieldController@update');
    Route::get('{id}/destroy', 'CustomFieldController@destroy');
});

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


Route::prefix('income')->group(function () {
    Route::get('/', 'IncomeController@index');
    Route::get('get_income', 'IncomeController@get_income');
    Route::get('create', 'IncomeController@create');
    Route::post('store', 'IncomeController@store');
    Route::get('{id}/show', 'IncomeController@show');
    Route::get('{id}/edit', 'IncomeController@edit');
    Route::post('{id}/update', 'IncomeController@update');
    Route::get('{id}/destroy', 'IncomeController@destroy');
    //categories
    Route::prefix('type')->group(function () {
        Route::get('/', 'IncomeTypeController@index');
        Route::get('get_income_types', 'IncomeTypeController@get_income_types');
        Route::get('create', 'IncomeTypeController@create');
        Route::post('store', 'IncomeTypeController@store');
        Route::get('{id}/show', 'IncomeTypeController@show');
        Route::get('{id}/edit', 'IncomeTypeController@edit');
        Route::post('{id}/update', 'IncomeTypeController@update');
        Route::get('{id}/destroy', 'IncomeTypeController@destroy');
    });
});
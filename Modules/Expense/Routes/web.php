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

Route::prefix('expense')->group(function () {
    Route::get('/', 'ExpenseController@index');
    Route::get('get_expenses', 'ExpenseController@get_expenses');
    Route::get('create', 'ExpenseController@create');
    Route::post('store', 'ExpenseController@store');
    Route::get('{id}/show', 'ExpenseController@show');
    Route::get('{id}/edit', 'ExpenseController@edit');
    Route::post('{id}/update', 'ExpenseController@update');
    Route::get('{id}/destroy', 'ExpenseController@destroy');
    //categories
    Route::prefix('type')->group(function () {
        Route::get('/', 'ExpenseTypeController@index');
        Route::get('get_expense_types', 'ExpenseTypeController@get_expense_types');
        Route::get('create', 'ExpenseTypeController@create');
        Route::post('store', 'ExpenseTypeController@store');
        Route::get('{id}/show', 'ExpenseTypeController@show');
        Route::get('{id}/edit', 'ExpenseTypeController@edit');
        Route::post('{id}/update', 'ExpenseTypeController@update');
        Route::get('{id}/destroy', 'ExpenseTypeController@destroy');
    });
});

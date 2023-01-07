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

Route::prefix('payroll')->group(function () {
    Route::get('/', 'PayrollController@index');
    Route::get('get_payroll', 'PayrollController@get_payroll');
    Route::get('create', 'PayrollController@create');
    Route::post('store', 'PayrollController@store');
    Route::get('{id}/show', 'PayrollController@show');
    Route::get('{id}/edit', 'PayrollController@edit');
    Route::post('{id}/update', 'PayrollController@update');
    Route::get('{id}/destroy', 'PayrollController@destroy');
    Route::get('{id}/pdf', 'PayrollController@pdf_payroll');
    Route::get('{id}/print', 'PayrollController@print_payroll');
    //payments
    Route::get('{id}/payment/', 'PayrollPaymentController@index');
    Route::get('{id}/payment/create', 'PayrollPaymentController@create');
    Route::post('{id}/payment/store', 'PayrollPaymentController@store');
    Route::get('payment/{id}/show', 'PayrollPaymentController@show');
    Route::get('payment/{id}/edit', 'PayrollPaymentController@edit');
    Route::post('payment/{id}/update', 'PayrollPaymentController@update');
    Route::get('payment/{id}/destroy', 'PayrollPaymentController@destroy');
    //items
    Route::prefix('item')->group(function () {
        Route::get('/', 'PayrollItemController@index');
        Route::get('create', 'PayrollItemController@create');
        Route::post('store', 'PayrollItemController@store');
        Route::get('{id}/show', 'PayrollItemController@show');
        Route::get('{id}/edit', 'PayrollItemController@edit');
        Route::post('{id}/update', 'PayrollItemController@update');
        Route::get('{id}/destroy', 'PayrollItemController@destroy');
    });
    //templates
    Route::prefix('template')->group(function () {
        Route::get('/', 'PayrollTemplateController@index');
        Route::get('create', 'PayrollTemplateController@create');
        Route::post('store', 'PayrollTemplateController@store');
        Route::get('{id}/show', 'PayrollTemplateController@show');
        Route::get('{id}/edit', 'PayrollTemplateController@edit');
        Route::post('{id}/update', 'PayrollTemplateController@update');
        Route::get('{id}/destroy', 'PayrollTemplateController@destroy');
    });
});

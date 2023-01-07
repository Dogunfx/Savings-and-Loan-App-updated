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

Route::prefix('core')->group(function () {
    Route::get('/', 'CoreController@index');
    Route::get('/create', 'CoreController@create');
});
//currencies
Route::prefix('currency')->group(function () {
    Route::get('/', 'CurrencyController@index');
    Route::get('get_currencies', 'CurrencyController@get_currencies');
    Route::get('create', 'CurrencyController@create');
    Route::post('store', 'CurrencyController@store');
    Route::get('{id}/show', 'CurrencyController@show');
    Route::get('{id}/edit', 'CurrencyController@edit');
    Route::post('{id}/update', 'CurrencyController@update');
    Route::get('{id}/destroy', 'CurrencyController@destroy');
});
//payment types
Route::prefix('payment_type')->group(function () {
    Route::get('/', 'PaymentTypeController@index');
    Route::get('get_payment_types', 'PaymentTypeController@get_payment_types');
    Route::get('get_payment_gateway', 'PaymentTypeController@get_payment_gateway');
    Route::get('create', 'PaymentTypeController@create');
    Route::post('store', 'PaymentTypeController@store');
    Route::get('{id}/show', 'PaymentTypeController@show');
    Route::get('{id}/edit', 'PaymentTypeController@edit');
    Route::post('{id}/update', 'PaymentTypeController@update');
    Route::get('{id}/destroy', 'PaymentTypeController@destroy');
});
//payment details
Route::prefix('payment_detail')->group(function () {
    Route::get('/', 'PaymentDetailController@index');
    Route::get('create', 'PaymentDetailController@create');
    Route::post('store', 'PaymentDetailController@store');
    Route::get('{id}/show', 'PaymentDetailController@show');
    Route::get('{id}/edit', 'PaymentDetailController@edit');
    Route::post('{id}/update', 'PaymentDetailController@update');
    Route::get('{id}/destroy', 'PaymentDetailController@destroy');
});
//tax rate
Route::prefix('tax_rate')->group(function () {
    Route::get('/', 'TaxRateController@index');
    Route::get('create', 'TaxRateController@create');
    Route::post('store', 'TaxRateController@store');
    Route::get('{id}/show', 'TaxRateController@show');
    Route::get('{id}/edit', 'TaxRateController@edit');
    Route::post('{id}/update', 'TaxRateController@update');
    Route::get('{id}/destroy', 'TaxRateController@destroy');
});
//modules
Route::prefix('module')->group(function () {
    Route::get('/', 'ModuleController@index');
    Route::get('disable', 'ModuleController@store_widget');
    Route::post('update_widget_positions', 'ModuleController@update_widget_positions');
    Route::get('upload', 'ModuleController@upload');
    Route::post('store', 'ModuleController@store');

});
//menu
Route::prefix('menu')->group(function () {
    Route::get('/', 'MenuController@index');
    Route::get('disable', 'MenuController@store_widget');
    Route::post('update', 'MenuController@update');
    Route::get('upload', 'MenuController@upload');
    Route::post('store', 'MenuController@store');

});
//themes
Route::prefix('theme')->group(function () {
    Route::get('/', 'ThemeController@index');
    Route::get('disable', 'ThemeController@store_widget');
    Route::post('update', 'ThemeController@update');
    Route::get('upload', 'ThemeController@upload');
    Route::post('store', 'ThemeController@store');

});
//payment gateways
Route::prefix('settings/payment_gateway')->group(function () {
    Route::get('/', 'PaymentGatewayController@index');
    Route::get('install', 'PaymentGatewayController@install');
    Route::post('update', 'PaymentGatewayController@update');
    Route::get('destroy', 'PaymentGatewayController@destroy');
});
<?php
Route::prefix('v1')->group(function () {
    //currencies
    Route::prefix('currency')->group(function () {
        Route::get('/', 'Api\v1\CurrencyController@index');
        Route::get('create', 'Api\v1\CurrencyController@create');
        Route::post('store', 'Api\v1\CurrencyController@store');
        Route::get('{id}/show', 'Api\v1\CurrencyController@show');
        Route::get('{id}/edit', 'Api\v1\CurrencyController@edit');
        Route::post('{id}/update', 'Api\v1\CurrencyController@update');
        Route::get('{id}/destroy', 'Api\v1\CurrencyController@destroy');
    });
//payment types
    Route::prefix('payment_type')->group(function () {
        Route::get('/', 'Api\v1\PaymentTypeController@index');
        Route::get('create', 'Api\v1\PaymentTypeController@create');
        Route::post('store', 'Api\v1\PaymentTypeController@store');
        Route::get('{id}/show', 'Api\v1\PaymentTypeController@show');
        Route::get('{id}/edit', 'Api\v1\PaymentTypeController@edit');
        Route::post('{id}/update', 'Api\v1\PaymentTypeController@update');
        Route::get('{id}/destroy', 'Api\v1\PaymentTypeController@destroy');
    });
});
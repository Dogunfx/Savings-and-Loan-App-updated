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

Route::prefix('flutterwave')->group(function () {
    Route::post('get_loan_intent', 'FlutterwaveController@get_loan_intent');
    Route::any('capture_payment', 'FlutterwaveController@capture_payment');
});
Route::any('webhooks/flutterwave', 'FlutterwaveController@webhook');
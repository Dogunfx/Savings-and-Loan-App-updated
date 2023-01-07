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

Route::prefix('paypal')->group(function() {
    Route::post('get_loan_intent', 'PaypalController@get_loan_intent');
    Route::any('capture_payment', 'PaypalController@capture_payment');
});
Route::any('webhooks/paypal', 'PaypalController@webhook');
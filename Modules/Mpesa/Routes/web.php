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

Route::prefix('mpesa')->group(function() {
    Route::post('get_loan_intent', 'MpesaController@get_loan_intent');
    Route::post('get_access_token', 'MpesaController@get_access_token');
    Route::any('capture_payment', 'MpesaController@capture_payment');
});
Route::any('webhooks/mpesa', 'MpesaController@webhook');
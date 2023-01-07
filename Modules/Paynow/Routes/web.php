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

Route::prefix('paynow')->group(function () {
    Route::any('webhook/paynow', 'PaynowController@webhook');
});
Route::any('webhooks/paynow', 'PaynowController@webhook');
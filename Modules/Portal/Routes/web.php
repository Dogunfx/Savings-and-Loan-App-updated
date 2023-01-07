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

Route::prefix('portal')->group(function () {
    Route::get('/', 'PortalController@index');
    Route::get('/dashboard', 'PortalController@index');
    //loans
    Route::prefix('loan')->group(function () {
        Route::get('/', 'LoanController@index');
        Route::get('get_loans', 'LoanController@get_loans');
        Route::get('{id}/show', 'LoanController@show');
        Route::post('switch_client', 'LoanController@switch_client');
        Route::get('{id}/transaction/create', 'LoanTransactionController@create');
        Route::post('{id}/transaction/store', 'LoanTransactionController@store');
        Route::get('transaction/{id}/show', 'LoanController@show_transaction');
        Route::get('transaction/{id}/pdf', 'LoanController@pdf_transaction');
        Route::get('transaction/{id}/print', 'LoanController@print_transaction');
        //schedules
        Route::get('{id}/schedule/show', 'LoanController@show_schedule');
        Route::get('{id}/schedule/pdf', 'LoanController@pdf_schedule');
        Route::get('{id}/schedule/print', 'LoanController@print_schedule');
        //applications
        Route::get('application', 'LoanController@application');
        Route::get('application/create', 'LoanController@create_application');
        Route::post('application/store', 'LoanController@store_application');
        Route::get('application/{id}/destroy', 'LoanController@destroy_application');
        //repayments
        Route::get('{id}/repayment/create', 'LoanController@create_repayment');
        Route::post('{id}/repayment/store', 'LoanController@store_repayment');
        Route::get('repayment/{id}/edit', 'LoanController@edit_repayment');
        Route::get('repayment/{id}/reverse', 'LoanController@reverse_repayment');
        Route::post('repayment/{id}/update', 'LoanController@update_repayment');
        Route::get('repayment/{id}/destroy', 'LoanController@destroy_repayment');
    });
    //savings
    Route::prefix('savings')->group(function () {
        Route::get('/', 'SavingsController@index');
        Route::get('get_savings', 'SavingsController@get_savings');
        Route::get('{id}/show', 'SavingsController@show');
        Route::get('{id}/transaction/create', 'SavingsController@create_transaction');
        Route::post('{id}/transaction/store', 'SavingsController@store_transaction');
        Route::get('transaction/{id}/show', 'SavingsController@show_transaction');
        Route::get('transaction/{id}/pdf', 'SavingsController@pdf_transaction');
        Route::get('transaction/{id}/print', 'SavingsController@print_transaction');
    });
    //client
    Route::prefix('client')->group(function () {
        Route::get('/', 'ClientController@index');
        Route::post('switch_client', 'ClientController@switch_client');
    });
});

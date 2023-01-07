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

Route::prefix('savings')->group(function () {
    Route::get('/', 'SavingsController@index');
    Route::get('get_savings', 'SavingsController@get_savings');
    Route::get('test', 'SavingsController@test');
    Route::get('create', 'SavingsController@create');
    Route::post('store', 'SavingsController@store');
    Route::get('{id}/show', 'SavingsController@show');
    Route::get('{id}/edit', 'SavingsController@edit');
    Route::post('{id}/update', 'SavingsController@update');
    Route::get('{id}/destroy', 'SavingsController@destroy');
    Route::post('{id}/approve_savings', 'SavingsController@approve_savings');
    Route::get('{id}/undo_approval', 'SavingsController@undo_approval');
    Route::post('{id}/reject_savings', 'SavingsController@reject_savings');
    Route::get('{id}/undo_rejection', 'SavingsController@undo_rejection');
    Route::post('{id}/withdraw_savings', 'SavingsController@withdraw_savings');
    Route::get('{id}/undo_withdrawn', 'SavingsController@undo_withdrawn');
    Route::post('{id}/activate_savings', 'SavingsController@activate_savings');
    Route::post('{id}/close_savings', 'SavingsController@close_savings');
    Route::get('{id}/undo_activation', 'SavingsController@undo_activation');
    Route::get('{id}/undo_closed', 'SavingsController@undo_closed');
    Route::post('{id}/inactive_savings', 'SavingsController@inactive_savings');
    Route::get('{id}/undo_inactive', 'SavingsController@undo_inactive');
    Route::post('{id}/dormant_savings', 'SavingsController@dormant_savings');
    Route::get('{id}/undo_dormant', 'SavingsController@undo_dormant');
    Route::post('{id}/change_savings_officer', 'SavingsController@change_savings_officer');
    Route::post('{id}/waive_interest', 'SavingsController@waive_interest');
    //transactions
    Route::get('{id}/transaction/create', 'SavingsController@create_transaction');
    Route::post('{id}/transaction/store', 'SavingsController@store_transaction');
    Route::get('transaction/{id}/show', 'SavingsController@show_transaction');
    Route::get('transaction/{id}/pdf', 'SavingsController@pdf_transaction');
    Route::get('transaction/{id}/print', 'SavingsController@print_transaction');
    Route::get('transaction/{id}/edit', 'SavingsController@edit_transaction');
    Route::post('transaction/{id}/update', 'SavingsController@update_transaction');
    Route::get('transaction/{id}/destroy', 'SavingsController@destroy_transaction');
    Route::get('transaction/{id}/reverse', 'SavingsController@reverse_transaction');
    //deposits
    Route::get('{id}/deposit/create', 'SavingsController@create_deposit');
    Route::post('{id}/deposit/store', 'SavingsController@store_deposit');
    Route::get('deposit/{id}/edit', 'SavingsController@edit_deposit');
    Route::post('deposit/{id}/update', 'SavingsController@update_deposit');
    //withdrawals
    Route::get('{id}/withdrawal/create', 'SavingsController@create_withdrawal');
    Route::post('{id}/withdrawal/store', 'SavingsController@store_withdrawal');
    Route::get('withdrawal/{id}/edit', 'SavingsController@edit_withdrawal');
    Route::post('withdrawal/{id}/update', 'SavingsController@update_withdrawal');
    //charges
    Route::get('charge/{id}/waive', 'SavingsController@waive_charge');
    Route::get('{id}/charge/create', 'SavingsController@create_savings_linked_charge');
    Route::post('{id}/charge/store', 'SavingsController@store_savings_linked_charge');
    Route::get('charge/{id}/pay', 'SavingsController@pay_charge');
    Route::post('charge/{id}/pay', 'SavingsController@store_pay_charge');
    //charges
    Route::prefix('charge')->group(function () {
        Route::get('/', 'SavingsChargeController@index');
        Route::get('get_charges', 'SavingsChargeController@get_charges');
        Route::get('create', 'SavingsChargeController@create');
        Route::post('store', 'SavingsChargeController@store');
        Route::get('{id}/show', 'SavingsChargeController@show');
        Route::get('{id}/edit', 'SavingsChargeController@edit');
        Route::post('{id}/update', 'SavingsChargeController@update');
        Route::get('{id}/destroy', 'SavingsChargeController@destroy');
    });
    //savings product
    Route::prefix('product')->group(function () {
        Route::get('/', 'SavingsProductController@index');
        Route::get('get_products', 'SavingsProductController@get_products');
        Route::get('create', 'SavingsProductController@create');
        Route::post('store', 'SavingsProductController@store');
        Route::get('{id}/show', 'SavingsProductController@show');
        Route::get('{id}/edit', 'SavingsProductController@edit');
        Route::post('{id}/update', 'SavingsProductController@update');
        Route::get('{id}/destroy', 'SavingsProductController@destroy');
        Route::get('{id}/get_charges', 'SavingsProductController@get_charges');
    });
});
//reports
Route::prefix('report')->group(function () {
    Route::get('savings', 'ReportController@index');
    Route::get('savings/transaction', 'ReportController@transaction');
    Route::get('savings/balance', 'ReportController@balance');
    Route::get('savings/account', 'ReportController@account');
    Route::get('savings/account_statement', 'ReportController@account_statement');
});
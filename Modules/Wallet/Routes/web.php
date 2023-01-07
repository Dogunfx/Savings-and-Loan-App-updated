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

Route::prefix('wallet')->group(function () {
    Route::get('/', 'WalletController@index');
    Route::get('get_wallets', 'WalletController@get_wallets');
    Route::get('create', 'WalletController@create');
    Route::post('store', 'WalletController@store');
    Route::get('{id}/show', 'WalletController@show');
    Route::get('{id}/edit', 'WalletController@edit');
    Route::post('{id}/update', 'WalletController@update');
    Route::post('{id}/approve_wallet', 'WalletController@approve_wallet');
    Route::get('{id}/undo_approval', 'WalletController@undo_approval');
    Route::post('{id}/reject_wallet', 'WalletController@reject_wallet');
    Route::get('{id}/undo_rejection', 'WalletController@undo_rejection');
    Route::post('{id}/close_wallet', 'WalletController@close_wallet');
    Route::get('{id}/undo_close', 'WalletController@undo_close');
    Route::post('{id}/approve_wallet', 'WalletController@approve_wallet');
    Route::get('{id}/undo_approval', 'WalletController@undo_approval');
    Route::get('{id}/destroy', 'WalletController@destroy');
    Route::get('{id}/deposit/create', 'WalletController@create_deposit');
    Route::post('{id}/deposit/store', 'WalletController@store_deposit');
    Route::get('{id}/transfer/loan/create', 'WalletController@create_loan_transfer');
    Route::post('{id}/transfer/loan/store', 'WalletController@store_loan_transfer');
    Route::get('{id}/transfer/savings/create', 'WalletController@create_savings_transfer');
    Route::post('{id}/transfer/savings/store', 'WalletController@store_savings_transfer');
    //transactions
    Route::prefix('transaction')->group(function () {
        Route::get('/', 'WalletTransactionController@index');
        Route::get('get_wallet_transactions', 'WalletTransactionController@get_wallet_transactions');
        Route::get('create', 'WalletTransactionController@create');
        Route::post('store', 'WalletTransactionController@store');
        Route::get('{id}/edit', 'WalletController@edit_transaction');
        Route::post('{id}/update', 'WalletController@update_transaction');
        Route::get('{id}/destroy', 'WalletController@destroy');
        Route::get('{id}/show', 'WalletController@show_transaction');
        Route::get('{id}/pdf', 'WalletController@pdf_transaction');
        Route::get('{id}/print', 'WalletController@print_transaction');
        Route::get('{id}/reverse', 'WalletController@reverse');

    });
});

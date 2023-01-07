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

Route::prefix('share')->group(function() {
    Route::get('/', 'ShareController@index');
    Route::get('get_shares', 'ShareController@get_shares');
    Route::get('test', 'ShareController@test');
    Route::get('create', 'ShareController@create');
    Route::post('store', 'ShareController@store');
    Route::get('{id}/show', 'ShareController@show');
    Route::get('{id}/edit', 'ShareController@edit');
    Route::post('{id}/update', 'ShareController@update');
    Route::get('{id}/destroy', 'ShareController@destroy');
    Route::post('{id}/approve_share', 'ShareController@approve_share');
    Route::get('{id}/undo_approval', 'ShareController@undo_approval');
    Route::post('{id}/reject_share', 'ShareController@reject_share');
    Route::get('{id}/undo_rejection', 'ShareController@undo_rejection');
    Route::post('{id}/withdraw_share', 'ShareController@withdraw_share');
    Route::get('{id}/undo_withdrawn', 'ShareController@undo_withdrawn');
    Route::post('{id}/activate_share', 'ShareController@activate_share');
    Route::post('{id}/close_share', 'ShareController@close_share');
    Route::get('{id}/undo_activation', 'ShareController@undo_activation');
    Route::get('{id}/undo_closed', 'ShareController@undo_closed');
    Route::post('{id}/inactive_share', 'ShareController@inactive_share');
    Route::get('{id}/undo_inactive', 'ShareController@undo_inactive');
    Route::post('{id}/dormant_share', 'ShareController@dormant_share');
    Route::get('{id}/undo_dormant', 'ShareController@undo_dormant');
    Route::post('{id}/change_share_officer', 'ShareController@change_share_officer');
    Route::post('{id}/waive_interest', 'ShareController@waive_interest');
    //transactions
    Route::get('{id}/transaction/create', 'ShareController@create_transaction');
    Route::post('{id}/transaction/store', 'ShareController@store_transaction');
    Route::post('{id}/redeem_share', 'ShareController@redeem_share');
    Route::post('{id}/purchase_share', 'ShareController@purchase_share');
    Route::get('transaction/{id}/show', 'ShareController@show_transaction');
    Route::get('transaction/{id}/pdf', 'ShareController@pdf_transaction');
    Route::get('transaction/{id}/print', 'ShareController@print_transaction');
    Route::get('transaction/{id}/edit', 'ShareController@edit_transaction');
    Route::post('transaction/{id}/update', 'ShareController@update_transaction');
    Route::get('transaction/{id}/destroy', 'ShareController@destroy_transaction');
    Route::get('transaction/{id}/reverse', 'ShareController@reverse_transaction');
    //deposits
    Route::get('{id}/deposit/create', 'ShareController@create_deposit');
    Route::post('{id}/deposit/store', 'ShareController@store_deposit');
    Route::get('deposit/{id}/edit', 'ShareController@edit_deposit');
    Route::post('deposit/{id}/update', 'ShareController@update_deposit');
    //withdrawals
    Route::get('{id}/withdrawal/create', 'ShareController@create_withdrawal');
    Route::post('{id}/withdrawal/store', 'ShareController@store_withdrawal');
    Route::get('withdrawal/{id}/edit', 'ShareController@edit_withdrawal');
    Route::post('withdrawal/{id}/update', 'ShareController@update_withdrawal');
    //charges
    Route::get('charge/{id}/waive', 'ShareController@waive_charge');
    Route::get('{id}/charge/create', 'ShareController@create_share_linked_charge');
    Route::post('{id}/charge/store', 'ShareController@store_share_linked_charge');
    Route::get('charge/{id}/pay', 'ShareController@pay_charge');
    Route::post('charge/{id}/pay', 'ShareController@store_pay_charge');
    //charges
    Route::prefix('charge')->group(function () {
        Route::get('/', 'ShareChargeController@index');
        Route::get('get_charges', 'ShareChargeController@get_charges');
        Route::get('create', 'ShareChargeController@create');
        Route::post('store', 'ShareChargeController@store');
        Route::get('{id}/show', 'ShareChargeController@show');
        Route::get('{id}/edit', 'ShareChargeController@edit');
        Route::post('{id}/update', 'ShareChargeController@update');
        Route::get('{id}/destroy', 'ShareChargeController@destroy');
    });
    //share product
    Route::prefix('product')->group(function () {
        Route::get('/', 'ShareProductController@index');
        Route::get('get_products', 'ShareProductController@get_products');
        Route::get('create', 'ShareProductController@create');
        Route::post('store', 'ShareProductController@store');
        Route::get('{id}/show', 'ShareProductController@show');
        Route::get('{id}/edit', 'ShareProductController@edit');
        Route::post('{id}/update', 'ShareProductController@update');
        Route::get('{id}/destroy', 'ShareProductController@destroy');
        Route::get('{id}/get_charges', 'ShareProductController@get_charges');
    });
});

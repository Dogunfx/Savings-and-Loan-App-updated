<?php

Route::prefix('v1')->group(function () {
    Route::prefix('savings')->group(function () {
        Route::get('/', 'Api\v1\SavingsController@index');
        Route::get('get_custom_fields', 'Api\v1\SavingsController@get_custom_fields');
        Route::get('test', 'Api\v1\SavingsController@test');
        Route::get('create', 'Api\v1\SavingsController@create');
        Route::post('store', 'Api\v1\SavingsController@store');
        Route::get('{id}/show', 'Api\v1\SavingsController@show');
        Route::get('{id}/edit', 'Api\v1\SavingsController@edit');
        Route::post('{id}/update', 'Api\v1\SavingsController@update');
        Route::get('{id}/destroy', 'Api\v1\SavingsController@destroy');
        Route::post('{id}/approve_savings', 'Api\v1\SavingsController@approve_savings');
        Route::get('{id}/undo_approval', 'Api\v1\SavingsController@undo_approval');
        Route::post('{id}/reject_savings', 'Api\v1\SavingsController@reject_savings');
        Route::get('{id}/undo_rejection', 'Api\v1\SavingsController@undo_rejection');
        Route::post('{id}/withdraw_savings', 'Api\v1\SavingsController@withdraw_savings');
        Route::get('{id}/undo_withdrawn', 'Api\v1\SavingsController@undo_withdrawn');
        Route::post('{id}/activate_savings', 'Api\v1\SavingsController@activate_savings');
        Route::post('{id}/close_savings', 'Api\v1\SavingsController@close_savings');
        Route::get('{id}/undo_activation', 'Api\v1\SavingsController@undo_activation');
        Route::get('{id}/undo_closed', 'Api\v1\SavingsController@undo_closed');
        Route::post('{id}/inactive_savings', 'Api\v1\SavingsController@inactive_savings');
        Route::get('{id}/undo_inactive', 'Api\v1\SavingsController@undo_inactive');
        Route::post('{id}/dormant_savings', 'Api\v1\SavingsController@dormant_savings');
        Route::get('{id}/undo_dormant', 'Api\v1\SavingsController@undo_dormant');
        Route::post('{id}/change_savings_officer', 'Api\v1\SavingsController@change_savings_officer');
        Route::post('{id}/waive_interest', 'Api\v1\SavingsController@waive_interest');
        //transactions
        Route::get('{id}/transaction/create', 'Api\v1\SavingsController@create_transaction');
        Route::post('{id}/transaction/store', 'Api\v1\SavingsController@store_transaction');
        Route::get('transaction/{id}/show', 'Api\v1\SavingsController@show_transaction');
        Route::get('transaction/{id}/pdf', 'Api\v1\SavingsController@pdf_transaction');
        Route::get('transaction/{id}/print', 'Api\v1\SavingsController@print_transaction');
        Route::get('transaction/{id}/edit', 'Api\v1\SavingsController@edit_transaction');
        Route::post('transaction/{id}/update', 'Api\v1\SavingsController@update_transaction');
        Route::get('transaction/{id}/destroy', 'Api\v1\SavingsController@destroy_transaction');
        Route::get('transaction/{id}/reverse', 'Api\v1\SavingsController@reverse_transaction');
        //deposits
        Route::get('{id}/deposit/create', 'Api\v1\SavingsController@create_deposit');
        Route::post('{id}/deposit/store', 'Api\v1\SavingsController@store_deposit');
        Route::get('deposit/{id}/edit', 'Api\v1\SavingsController@edit_deposit');
        Route::post('deposit/{id}/update', 'Api\v1\SavingsController@update_deposit');
        //withdrawals
        Route::get('{id}/withdrawal/create', 'Api\v1\SavingsController@create_withdrawal');
        Route::post('{id}/withdrawal/store', 'Api\v1\SavingsController@store_withdrawal');
        Route::get('withdrawal/{id}/edit', 'Api\v1\SavingsController@edit_withdrawal');
        Route::post('withdrawal/{id}/update', 'Api\v1\SavingsController@update_withdrawal');
        //charges
        Route::get('charge/{id}/waive', 'Api\v1\SavingsController@waive_charge');
        Route::get('{id}/charge/create', 'Api\v1\SavingsController@create_savings_linked_charge');
        Route::post('{id}/charge/store', 'Api\v1\SavingsController@store_savings_linked_charge');
        Route::get('charge/{id}/pay', 'Api\v1\SavingsController@pay_charge');
        Route::post('charge/{id}/pay', 'Api\v1\SavingsController@store_pay_charge');
        //charges
        Route::prefix('charge')->group(function () {
            Route::get('/', 'Api\v1\SavingsChargeController@index');
            Route::get('get_charges', 'Api\v1\SavingsChargeController@get_charges');
            Route::get('create', 'Api\v1\SavingsChargeController@create');
            Route::post('store', 'Api\v1\SavingsChargeController@store');
            Route::get('{id}/show', 'Api\v1\SavingsChargeController@show');
            Route::get('{id}/edit', 'Api\v1\SavingsChargeController@edit');
            Route::post('{id}/update', 'Api\v1\SavingsChargeController@update');
            Route::get('{id}/destroy', 'Api\v1\SavingsChargeController@destroy');
        });
        //savings product
        Route::prefix('product')->group(function () {
            Route::get('/', 'Api\v1\SavingsProductController@index');
            Route::get('get_products', 'Api\v1\SavingsProductController@get_products');
            Route::get('create', 'Api\v1\SavingsProductController@create');
            Route::post('store', 'Api\v1\SavingsProductController@store');
            Route::get('{id}/show', 'Api\v1\SavingsProductController@show');
            Route::get('{id}/edit', 'Api\v1\SavingsProductController@edit');
            Route::post('{id}/update', 'Api\v1\SavingsProductController@update');
            Route::get('{id}/destroy', 'Api\v1\SavingsProductController@destroy');
            Route::get('{id}/get_charges', 'Api\v1\SavingsProductController@get_charges');
        });
    });
    Route::prefix('report')->group(function () {
        Route::get('savings/transaction', 'Api\v1\ReportController@transaction');
        Route::get('savings/balance', 'Api\v1\ReportController@balance');
        Route::get('savings/account', 'Api\v1\ReportController@account');
    });
});
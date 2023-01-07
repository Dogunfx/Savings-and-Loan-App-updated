<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('loan')->group(function () {
        Route::get('/', 'Api\v1\LoanController@index');
        Route::get('get_custom_fields', 'Api\v1\LoanController@get_custom_fields');
        Route::get('create', 'Api\v1\LoanController@create');
        Route::post('store', 'Api\v1\LoanController@store');
        Route::get('calculator', 'Api\v1\LoanController@create_loan_calculator');
        Route::post('calculator', 'Api\v1\LoanController@process_loan_calculator');
        Route::get('{id}/show', 'Api\v1\LoanController@show');
        Route::get('{id}/edit', 'Api\v1\LoanController@edit');
        Route::post('{id}/update', 'Api\v1\LoanController@update');
        Route::get('{id}/destroy', 'Api\v1\LoanController@destroy');
        Route::post('{id}/approve_loan', 'Api\v1\LoanController@approve_loan');
        Route::get('{id}/undo_approval', 'Api\v1\LoanController@undo_approval');
        Route::post('{id}/reject_loan', 'Api\v1\LoanController@reject_loan');
        Route::get('{id}/undo_rejection', 'Api\v1\LoanController@undo_rejection');
        Route::post('{id}/withdraw_loan', 'Api\v1\LoanController@withdraw_loan');
        Route::get('{id}/undo_withdrawn', 'Api\v1\LoanController@undo_withdrawn');
        Route::post('{id}/disburse_loan', 'Api\v1\LoanController@disburse_loan');
        Route::get('{id}/undo_disbursement', 'Api\v1\LoanController@undo_disbursement');
        Route::post('{id}/write_off_loan', 'Api\v1\LoanController@write_off_loan');
        Route::get('{id}/undo_write_off', 'Api\v1\LoanController@undo_write_off');
        Route::post('{id}/change_loan_officer', 'Api\v1\LoanController@change_loan_officer');
        Route::post('{id}/waive_interest', 'Api\v1\LoanController@waive_interest');
//applications
        Route::get('application', 'Api\v1\LoanController@application');
        Route::get('application/{id}/show', 'LoanController@show_application');
        Route::get('get_applications', 'LoanController@get_applications');
        Route::get('application/{id}/reject', 'LoanController@reject_application');
        Route::get('application/{id}/undo_reject', 'LoanController@undo_reject_application');
        Route::get('application/{id}/approve', 'LoanController@approve_application');
        Route::post('application/{id}/store_approve', 'LoanController@store_approve_application');
        //loan files
        Route::get('{id}/file/create', 'Api\v1\LoanFileController@create');
        Route::post('{id}/file/store', 'Api\v1\LoanFileController@store');
        Route::get('{id}/file/show', 'Api\v1\LoanFileController@show');
        Route::get('file/{id}/edit', 'Api\v1\LoanFileController@edit');
        Route::post('file/{id}/update', 'Api\v1\LoanFileController@update');
        Route::get('file/{id}/destroy', 'Api\v1\LoanFileController@destroy');
        //collateral
        Route::get('{id}/collateral/create', 'Api\v1\LoanCollateralController@create');
        Route::post('{id}/collateral/store', 'Api\v1\LoanCollateralController@store');
        Route::get('{id}/collateral/show', 'Api\v1\LoanCollateralController@show');
        Route::get('collateral/{id}/edit', 'Api\v1\LoanCollateralController@edit');
        Route::post('collateral/{id}/update', 'Api\v1\LoanCollateralController@update');
        Route::get('collateral/{id}/destroy', 'Api\v1\LoanCollateralController@destroy');
        //notes
        Route::get('{id}/note/create', 'Api\v1\LoanNoteController@create');
        Route::post('{id}/note/store', 'Api\v1\LoanNoteController@store');
        Route::get('{id}/note/show', 'Api\v1\LoanNoteController@show');
        Route::get('note/{id}/edit', 'Api\v1\LoanNoteController@edit');
        Route::post('note/{id}/update', 'Api\v1\LoanNoteController@update');
        Route::get('note/{id}/destroy', 'Api\v1\LoanNoteController@destroy');
        //guarantor
        Route::get('{id}/guarantor/create', 'Api\v1\LoanGuarantorController@create');
        Route::post('{id}/guarantor/store', 'Api\v1\LoanGuarantorController@store');
        Route::get('guarantor/{id}/show', 'Api\v1\LoanGuarantorController@show');
        Route::get('guarantor/{id}/edit', 'Api\v1\LoanGuarantorController@edit');
        Route::post('guarantor/{id}/update', 'Api\v1\LoanGuarantorController@update');
        Route::get('guarantor/{id}/destroy', 'Api\v1\LoanGuarantorController@destroy');
        //loan transactions

        Route::get('transaction/{id}/show', 'Api\v1\LoanController@show_transaction');

        //schedules
        Route::get('{id}/schedule/show', 'Api\v1\LoanController@show_schedule');

        //repayments
        Route::get('{id}/repayment/create', 'Api\v1\LoanController@create_repayment');
        Route::post('{id}/repayment/store', 'Api\v1\LoanController@store_repayment');
        Route::get('repayment/{id}/edit', 'Api\v1\LoanController@edit_repayment');
        Route::get('repayment/{id}/reverse', 'Api\v1\LoanController@reverse_repayment');
        Route::post('repayment/{id}/update', 'Api\v1\LoanController@update_repayment');
        Route::get('repayment/{id}/destroy', 'Api\v1\LoanController@destroy_repayment');
        //charges
        Route::get('charge/{id}/waive', 'Api\v1\LoanController@waive_charge');
        Route::get('{id}/charge/create', 'Api\v1\LoanController@create_loan_linked_charge');
        Route::post('{id}/charge/store', 'Api\v1\LoanController@store_loan_linked_charge');
        //funds
        Route::prefix('fund')->group(function () {
            Route::get('/', 'Api\v1\FundController@index');
            Route::get('create', 'Api\v1\FundController@create');
            Route::post('store', 'Api\v1\FundController@store');
            Route::get('{id}/show', 'Api\v1\FundController@show');
            Route::get('{id}/edit', 'Api\v1\FundController@edit');
            Route::post('{id}/update', 'Api\v1\FundController@update');
            Route::get('{id}/destroy', 'Api\v1\FundController@destroy');
        });
        //purposes
        Route::prefix('purpose')->group(function () {
            Route::get('/', 'Api\v1\LoanPurposeController@index');
            Route::get('create', 'Api\v1\LoanPurposeController@create');
            Route::post('store', 'Api\v1\LoanPurposeController@store');
            Route::get('{id}/show', 'Api\v1\LoanPurposeController@show');
            Route::get('{id}/edit', 'Api\v1\LoanPurposeController@edit');
            Route::post('{id}/update', 'Api\v1\LoanPurposeController@update');
            Route::get('{id}/destroy', 'Api\v1\LoanPurposeController@destroy');
        });
        //collateral types
        Route::prefix('collateral_type')->group(function () {
            Route::get('/', 'Api\v1\LoanCollateralTypeController@index');
            Route::get('create', 'Api\v1\LoanCollateralTypeController@create');
            Route::post('store', 'Api\v1\LoanCollateralTypeController@store');
            Route::get('{id}/show', 'Api\v1\LoanCollateralTypeController@show');
            Route::get('{id}/edit', 'Api\v1\LoanCollateralTypeController@edit');
            Route::post('{id}/update', 'Api\v1\LoanCollateralTypeController@update');
            Route::get('{id}/destroy', 'Api\v1\LoanCollateralTypeController@destroy');
        });
        //credit checks
        Route::prefix('credit_check')->group(function () {
            Route::get('/', 'Api\v1\LoanCreditCheckController@index');
            Route::get('{id}/show', 'Api\v1\LoanCreditCheckController@show');
            Route::get('{id}/edit', 'Api\v1\LoanCreditCheckController@edit');
            Route::post('{id}/update', 'Api\v1\LoanCreditCheckController@update');
        });
        //charges
        Route::prefix('charge')->group(function () {
            Route::get('/', 'Api\v1\LoanChargeController@index');
            Route::get('get_charge_types', 'Api\v1\LoanChargeController@get_charge_types');
            Route::get('get_charge_options', 'Api\v1\LoanChargeController@get_charge_options');
            Route::get('create', 'Api\v1\LoanChargeController@create');
            Route::post('store', 'Api\v1\LoanChargeController@store');
            Route::get('{id}/show', 'Api\v1\LoanChargeController@show');
            Route::get('{id}/edit', 'Api\v1\LoanChargeController@edit');
            Route::post('{id}/update', 'Api\v1\LoanChargeController@update');
            Route::get('{id}/destroy', 'Api\v1\LoanChargeController@destroy');
        });
        //loan product
        Route::prefix('product')->group(function () {
            Route::get('/', 'Api\v1\LoanProductController@index');
            Route::get('get_loan_transaction_processing_strategies', 'Api\v1\LoanProductController@get_loan_transaction_processing_strategies');
            Route::get('create', 'Api\v1\LoanProductController@create');
            Route::post('store', 'Api\v1\LoanProductController@store');
            Route::get('{id}/show', 'Api\v1\LoanProductController@show');
            Route::get('{id}/edit', 'Api\v1\LoanProductController@edit');
            Route::post('{id}/update', 'Api\v1\LoanProductController@update');
            Route::get('{id}/destroy', 'Api\v1\LoanProductController@destroy');
            Route::get('{id}/get_charges', 'Api\v1\LoanProductController@get_charges');
        });

    });
    Route::prefix('report')->group(function () {
        Route::get('loan', 'Api\v1\ReportController@index');
        Route::get('loan/collection_sheet', 'Api\v1\ReportController@collection_sheet');
        Route::get('loan/repayment', 'Api\v1\ReportController@repayment');
        Route::get('loan/expected_repayment', 'Api\v1\ReportController@expected_repayment');
        Route::get('loan/arrears', 'Api\v1\ReportController@arrears');
        Route::get('loan/disbursement', 'Api\v1\ReportController@disbursement');
        Route::get('loan/portfolio_at_risk', 'Api\v1\ReportController@portfolio_at_risk');
    });
});

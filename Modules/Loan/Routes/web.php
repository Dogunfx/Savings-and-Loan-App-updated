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

use Illuminate\Support\Facades\Route;

Route::get('loan/test', 'LoanController@test');
Route::prefix('repayment')->group(function () {
    Route::get('/', 'RepaymentController@index');
});
Route::prefix('loan')->group(function () {
    Route::get('/', 'LoanController@index');
    Route::get('get_loans', 'LoanController@get_loans');
    Route::get('create', 'LoanController@create');
    Route::get('create_client_loan', 'LoanController@create_client_loan');
    Route::post('store', 'LoanController@store');
    Route::get('calculator', 'LoanController@create_loan_calculator');
    Route::post('calculator', 'LoanController@process_loan_calculator');
    Route::get('{id}/show', 'LoanController@show');
    Route::get('{id}/edit', 'LoanController@edit');
    Route::post('{id}/update', 'LoanController@update');
    Route::get('{id}/destroy', 'LoanController@destroy');
    Route::post('{id}/approve_loan', 'LoanController@approve_loan');
    Route::get('{id}/undo_approval', 'LoanController@undo_approval');
    Route::post('{id}/reject_loan', 'LoanController@reject_loan');
    Route::get('{id}/undo_rejection', 'LoanController@undo_rejection');
    Route::post('{id}/withdraw_loan', 'LoanController@withdraw_loan');
    Route::get('{id}/undo_withdrawn', 'LoanController@undo_withdrawn');
    Route::post('{id}/disburse_loan', 'LoanController@disburse_loan');
    Route::get('{id}/undo_disbursement', 'LoanController@undo_disbursement');
    Route::post('{id}/write_off_loan', 'LoanController@write_off_loan');
    Route::get('{id}/undo_write_off', 'LoanController@undo_write_off');
    Route::post('{id}/reschedule_loan', 'LoanController@reschedule_loan');
    Route::get('{id}/undo_reschedule', 'LoanController@undo_reschedule');
    Route::post('{id}/change_loan_officer', 'LoanController@change_loan_officer');
    Route::post('{id}/waive_interest', 'LoanController@waive_interest');
//applications
    Route::get('application', 'LoanController@application');
    Route::get('application/{id}/show', 'LoanController@show_application');
    Route::get('get_applications', 'LoanController@get_applications');
    Route::get('application/{id}/reject', 'LoanController@reject_application');
    Route::get('application/{id}/undo_reject', 'LoanController@undo_reject_application');
    Route::get('application/{id}/approve', 'LoanController@approve_application');
    Route::post('application/{id}/store_approve', 'LoanController@store_approve_application');
    //loan files
    Route::get('{id}/file/create', 'LoanFileController@create');
    Route::post('{id}/file/store', 'LoanFileController@store');
    Route::get('{id}/file/show', 'LoanFileController@show');
    Route::get('file/{id}/edit', 'LoanFileController@edit');
    Route::post('file/{id}/update', 'LoanFileController@update');
    Route::get('file/{id}/destroy', 'LoanFileController@destroy');
    //collateral
    Route::get('{id}/collateral/create', 'LoanCollateralController@create');
    Route::post('{id}/collateral/store', 'LoanCollateralController@store');
    Route::get('{id}/collateral/show', 'LoanCollateralController@show');
    Route::get('collateral/{id}/edit', 'LoanCollateralController@edit');
    Route::post('collateral/{id}/update', 'LoanCollateralController@update');
    Route::get('collateral/{id}/destroy', 'LoanCollateralController@destroy');
    //notes
    Route::get('{id}/note/create', 'LoanNoteController@create');
    Route::post('{id}/note/store', 'LoanNoteController@store');
    Route::get('{id}/note/show', 'LoanNoteController@show');
    Route::get('note/{id}/edit', 'LoanNoteController@edit');
    Route::post('note/{id}/update', 'LoanNoteController@update');
    Route::get('note/{id}/destroy', 'LoanNoteController@destroy');
    //guarantor
    Route::get('{id}/guarantor/create', 'LoanGuarantorController@create');
    Route::post('{id}/guarantor/store', 'LoanGuarantorController@store');
    Route::get('guarantor/{id}/show', 'LoanGuarantorController@show');
    Route::get('guarantor/{id}/edit', 'LoanGuarantorController@edit');
    Route::post('guarantor/{id}/update', 'LoanGuarantorController@update');
    Route::get('guarantor/{id}/destroy', 'LoanGuarantorController@destroy');
    //loan transactions

    Route::get('transaction/{id}/show', 'LoanController@show_transaction');
    Route::get('transaction/{id}/pdf', 'LoanController@pdf_transaction');
    Route::get('transaction/{id}/print', 'LoanController@print_transaction');

    //schedules
    Route::get('{id}/schedule/show', 'LoanController@show_schedule');
    Route::get('{id}/schedule/pdf', 'LoanController@pdf_schedule');
    Route::get('{id}/schedule/print', 'LoanController@print_schedule');
    //repayments
    Route::get('{id}/repayment/create', 'LoanController@create_repayment');
    Route::post('{id}/repayment/store', 'LoanController@store_repayment');
    Route::get('repayment/{id}/edit', 'LoanController@edit_repayment');
    Route::get('repayment/{id}/reverse', 'LoanController@reverse_repayment');
    Route::post('repayment/{id}/update', 'LoanController@update_repayment');
    Route::get('repayment/{id}/destroy', 'LoanController@destroy_repayment');
    //charges
    Route::get('charge/{id}/waive', 'LoanController@waive_charge');
    Route::get('{id}/charge/create', 'LoanController@create_loan_linked_charge');
    Route::post('{id}/charge/store', 'LoanController@store_loan_linked_charge');
    //funds
    Route::prefix('fund')->group(function () {
        Route::get('/', 'FundController@index');
        Route::get('get_funds', 'FundController@get_funds');
        Route::get('create', 'FundController@create');
        Route::post('store', 'FundController@store');
        Route::get('{id}/show', 'FundController@show');
        Route::get('{id}/edit', 'FundController@edit');
        Route::post('{id}/update', 'FundController@update');
        Route::get('{id}/destroy', 'FundController@destroy');
    });
    //purposes
    Route::prefix('purpose')->group(function () {
        Route::get('/', 'LoanPurposeController@index');
        Route::get('get_purposes', 'LoanPurposeController@get_purposes');
        Route::get('create', 'LoanPurposeController@create');
        Route::post('store', 'LoanPurposeController@store');
        Route::get('{id}/show', 'LoanPurposeController@show');
        Route::get('{id}/edit', 'LoanPurposeController@edit');
        Route::post('{id}/update', 'LoanPurposeController@update');
        Route::get('{id}/destroy', 'LoanPurposeController@destroy');
    });
    //collateral types
    Route::prefix('collateral_type')->group(function () {
        Route::get('/', 'LoanCollateralTypeController@index');
        Route::get('get_collateral_types', 'LoanCollateralTypeController@get_collateral_types');
        Route::get('create', 'LoanCollateralTypeController@create');
        Route::post('store', 'LoanCollateralTypeController@store');
        Route::get('{id}/show', 'LoanCollateralTypeController@show');
        Route::get('{id}/edit', 'LoanCollateralTypeController@edit');
        Route::post('{id}/update', 'LoanCollateralTypeController@update');
        Route::get('{id}/destroy', 'LoanCollateralTypeController@destroy');
    });
    //credit checks
    Route::prefix('credit_check')->group(function () {
        Route::get('/', 'LoanCreditCheckController@index');
        Route::get('{id}/show', 'LoanCreditCheckController@show');
        Route::get('{id}/edit', 'LoanCreditCheckController@edit');
        Route::post('{id}/update', 'LoanCreditCheckController@update');
    });
    //charges
    Route::prefix('charge')->group(function () {
        Route::get('/', 'LoanChargeController@index');
        Route::get('get_charges', 'LoanChargeController@get_charges');
        Route::get('get_charge_types', 'LoanChargeController@get_charge_types');
        Route::get('get_charge_options', 'LoanChargeController@get_charge_options');
        Route::get('create', 'LoanChargeController@create');
        Route::post('store', 'LoanChargeController@store');
        Route::get('{id}/show', 'LoanChargeController@show');
        Route::get('{id}/edit', 'LoanChargeController@edit');
        Route::post('{id}/update', 'LoanChargeController@update');
        Route::get('{id}/destroy', 'LoanChargeController@destroy');
    });
    //loan product
    Route::prefix('product')->group(function () {
        Route::get('/', 'LoanProductController@index');
        Route::get('get_products', 'LoanProductController@get_products');
        Route::get('create', 'LoanProductController@create');
        Route::post('store', 'LoanProductController@store');
        Route::get('{id}/show', 'LoanProductController@show');
        Route::get('{id}/edit', 'LoanProductController@edit');
        Route::post('{id}/update', 'LoanProductController@update');
        Route::get('{id}/destroy', 'LoanProductController@destroy');
        Route::get('{id}/get_charges', 'LoanProductController@get_charges');
    });

});
//reports
Route::prefix('report')->group(function () {
    Route::get('loan', 'ReportController@index');
    Route::get('loan/collection_sheet', 'ReportController@collection_sheet');
    Route::get('loan/repayment', 'ReportController@repayment');
    Route::get('loan/expected_repayment', 'ReportController@expected_repayment');
    Route::get('loan/arrears', 'ReportController@arrears');
    Route::get('loan/disbursement', 'ReportController@disbursement');
    Route::get('loan/portfolio_at_risk', 'ReportController@portfolio_at_risk');
});
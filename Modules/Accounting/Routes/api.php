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
    Route::prefix('accounting')->group(function () {
        Route::get('trial_balance', 'AccountingController@trial_balance');
        Route::get('ledger', 'AccountingController@ledger');
        Route::get('balance_sheet', 'AccountingController@balance_sheet');
        Route::get('income_statement', 'AccountingController@income_statement');
        Route::get('cash_flow', 'AccountingController@cash_flow');
        //chart of accounts
        Route::prefix('chart_of_account')->group(function () {
            Route::get('/', 'Api\v1\ChartOfAccountController@index');
            Route::get('get_accounts_via_type', 'Api\v1\ChartOfAccountController@get_accounts_via_type');
            Route::get('create', 'Api\v1\ChartOfAccountController@create');
            Route::post('store', 'Api\v1\ChartOfAccountController@store');
            Route::get('{id}/show', 'Api\v1\ChartOfAccountController@show');
            Route::get('{id}/edit', 'Api\v1\ChartOfAccountController@edit');
            Route::post('{id}/update', 'Api\v1\ChartOfAccountController@update');
            Route::get('{id}/destroy', 'Api\v1\ChartOfAccountController@destroy');
        });
        //journal entries
        Route::prefix('journal_entry')->group(function () {
            Route::get('/', 'Api\v1\JournalEntryController@index');
            Route::get('create', 'Api\v1\JournalEntryController@create');
            Route::post('store', 'Api\v1\JournalEntryController@store');
            Route::get('{id}/show', 'Api\v1\JournalEntryController@show');
            Route::get('{id}/edit', 'Api\v1\JournalEntryController@edit');
            Route::get('{id}/reverse', 'Api\v1\JournalEntryController@reverse');
            Route::post('{id}/update', 'Api\v1\JournalEntryController@update');
            Route::get('{id}/destroy', 'Api\v1\JournalEntryController@destroy');
        });
    });
    Route::prefix('report')->group(function () {
        Route::get('accounting', 'ReportController@index');
        Route::get('accounting/trial_balance', 'ReportController@trial_balance');
        Route::get('accounting/ledger', 'ReportController@ledger');
        Route::get('accounting/balance_sheet', 'ReportController@balance_sheet');
        Route::get('accounting/income_statement', 'ReportController@income_statement');
        Route::get('accounting/cash_flow', 'ReportController@cash_flow');
    });
});
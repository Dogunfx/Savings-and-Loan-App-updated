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

Route::prefix('accounting')->group(function () {
    Route::get('trial_balance', 'AccountingController@trial_balance');
    Route::get('ledger', 'AccountingController@ledger');
    Route::get('balance_sheet', 'AccountingController@balance_sheet');
    Route::get('income_statement', 'AccountingController@income_statement');
    Route::get('cash_flow', 'AccountingController@cash_flow');
    //chart of accounts
    Route::prefix('chart_of_account')->group(function () {
        Route::get('/', 'ChartOfAccountController@index');
        Route::get('get_chart_of_accounts', 'ChartOfAccountController@get_chart_of_accounts');
        Route::get('create', 'ChartOfAccountController@create');
        Route::post('store', 'ChartOfAccountController@store');
        Route::get('{id}/show', 'ChartOfAccountController@show');
        Route::get('{id}/edit', 'ChartOfAccountController@edit');
        Route::post('{id}/update', 'ChartOfAccountController@update');
        Route::get('{id}/destroy', 'ChartOfAccountController@destroy');
    });
    //journal entries
    Route::prefix('journal_entry')->group(function () {
        Route::get('/', 'JournalEntryController@index');
        Route::get('get_journal_entries', 'JournalEntryController@get_journal_entries');
        Route::get('create', 'JournalEntryController@create');
        Route::post('store', 'JournalEntryController@store');
        Route::get('{id}/show', 'JournalEntryController@show');
        Route::get('{id}/edit', 'JournalEntryController@edit');
        Route::get('{id}/reverse', 'JournalEntryController@reverse');
        Route::post('{id}/update', 'JournalEntryController@update');
        Route::get('{id}/destroy', 'JournalEntryController@destroy');
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



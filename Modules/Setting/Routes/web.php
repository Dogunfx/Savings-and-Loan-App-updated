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

Route::prefix('setting')->group(function () {
    Route::get('/', 'SettingController@index');
    Route::get('organisation', 'SettingController@organisation');
    Route::get('general', 'SettingController@general');
    Route::get('email', 'SettingController@email');
    Route::get('sms', 'SettingController@sms');
    Route::get('system', 'SettingController@system');
    Route::post('update', 'SettingController@update');
    Route::get('system_update', 'SettingController@system_update');
    Route::get('other', 'SettingController@other');
});

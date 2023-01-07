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

Route::prefix('communication')->group(function () {
    Route::get('/', 'CommunicationController@index');
    //sms gateway
    Route::prefix('sms_gateway')->group(function () {
        Route::get('/', 'SmsGatewayController@index');
        Route::get('get_sms_gateways', 'SmsGatewayController@get_sms_gateways');
        Route::get('create', 'SmsGatewayController@create');
        Route::post('store', 'SmsGatewayController@store');
        Route::get('show', 'SmsGatewayController@show');
        Route::get('{id}/edit', 'SmsGatewayController@edit');
        Route::post('{id}/update', 'SmsGatewayController@update');
        Route::get('{id}/destroy', 'SmsGatewayController@destroy');
    });
    Route::prefix('campaign')->group(function () {
        Route::get('/', 'CommunicationCampaignController@index');
        Route::get('get_communication_campaigns', 'CommunicationCampaignController@get_communication_campaigns');
        Route::get('create', 'CommunicationCampaignController@create');
        Route::post('store', 'CommunicationCampaignController@store');
        Route::get('{id}/show', 'CommunicationCampaignController@show');
        Route::get('{id}/edit', 'CommunicationCampaignController@edit');
        Route::post('{id}/update', 'CommunicationCampaignController@update');
        Route::get('{id}/destroy', 'CommunicationCampaignController@destroy');
    });
    Route::prefix('log')->group(function () {
        Route::get('/', 'CommunicationLogController@index');
        Route::get('get_logs', 'CommunicationLogController@get_logs');
        Route::get('create', 'CommunicationLogController@create');
        Route::post('store', 'CommunicationLogController@store');
        Route::get('show', 'CommunicationLogController@show');
        Route::get('{id}/edit', 'CommunicationLogController@edit');
        Route::post('{id}/update', 'CommunicationLogController@update');
        Route::get('{id}/destroy', 'CommunicationLogController@destroy');
    });
});

<?php


Route::prefix('v1')->group(function () {
    Route::prefix('communication')->group(function () {
        Route::get('/', 'Api\v1\CommunicationController@index');
        //sms gateway
        Route::prefix('sms_gateway')->group(function () {
            Route::get('/', 'Api\v1\SmsGatewayController@index');
            Route::get('create', 'Api\v1\SmsGatewayController@create');
            Route::post('store', 'Api\v1\SmsGatewayController@store');
            Route::get('{id}/show', 'Api\v1\SmsGatewayController@show');
            Route::get('{id}/edit', 'Api\v1\SmsGatewayController@edit');
            Route::post('{id}/update', 'Api\v1\SmsGatewayController@update');
            Route::get('{id}/destroy', 'Api\v1\SmsGatewayController@destroy');
        });
        Route::prefix('campaign')->group(function () {
            Route::get('/', 'Api\v1\CommunicationCampaignController@index');
            Route::get('get_business_rules', 'Api\v1\CommunicationCampaignController@get_business_rules');
            Route::get('get_attachments_types', 'Api\v1\CommunicationCampaignController@get_attachments_types');
            Route::get('create', 'Api\v1\CommunicationCampaignController@create');
            Route::post('store', 'Api\v1\CommunicationCampaignController@store');
            Route::get('{id}/show', 'Api\v1\CommunicationCampaignController@show');
            Route::get('{id}/edit', 'Api\v1\CommunicationCampaignController@edit');
            Route::post('{id}/update', 'Api\v1\CommunicationCampaignController@update');
            Route::get('{id}/destroy', 'Api\v1\CommunicationCampaignController@destroy');
        });
        Route::prefix('log')->group(function () {
            Route::get('/', 'Api\v1\CommunicationLogController@index');
            Route::get('get_logs', 'Api\v1\CommunicationLogController@get_logs');
            Route::get('create', 'Api\v1\CommunicationLogController@create');
            Route::post('store', 'Api\v1\CommunicationLogController@store');
            Route::get('{id}/show', 'Api\v1\CommunicationLogController@show');
            Route::get('{id}/edit', 'Api\v1\CommunicationLogController@edit');
            Route::post('{id}/update', 'Api\v1\CommunicationLogController@update');
            Route::get('{id}/destroy', 'Api\v1\CommunicationLogController@destroy');
        });
    });
});
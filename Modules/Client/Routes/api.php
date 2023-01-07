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
    Route::prefix('client')->group(function () {
        Route::get('/', 'Api\v1\ClientController@index');
        Route::get('get_clients', 'Api\v1\ClientController@get_clients');
        Route::get('get_custom_fields', 'Api\v1\ClientController@get_custom_fields');
        Route::post('store', 'Api\v1\ClientController@store');
        Route::get('{id}/show', 'Api\v1\ClientController@show');
        Route::get('{id}/edit', 'Api\v1\ClientController@edit');
        Route::post('{id}/update', 'Api\v1\ClientController@update');
        Route::get('{id}/destroy', 'Api\v1\ClientController@destroy');
        Route::get('{id}/user/create', 'Api\v1\ClientController@create_user');
        Route::post('{id}/user/store', 'Api\v1\ClientController@store_user');
        Route::get('user/{id}/destroy', 'Api\v1\ClientController@destroy_user');
        Route::post('{id}/change_status', 'Api\v1\ClientController@change_status');
        //client identification
        Route::get('{id}/client_identification/create', 'Api\v1\ClientIdentificationController@create');
        Route::post('{id}/client_identification/store', 'Api\v1\ClientIdentificationController@store');
        Route::get('{id}/client_identification/show', 'Api\v1\ClientIdentificationController@show');
        Route::get('client_identification/{id}/edit', 'Api\v1\ClientIdentificationController@edit');
        Route::post('client_identification/{id}/update', 'Api\v1\ClientIdentificationController@update');
        Route::get('client_identification/{id}/destroy', 'Api\v1\ClientIdentificationController@destroy');
        //client next of kin
        Route::get('{id}/client_next_of_kin/create', 'Api\v1\ClientNextOfKinController@create');
        Route::post('{id}/client_next_of_kin/store', 'Api\v1\ClientNextOfKinController@store');
        Route::get('{id}/client_next_of_kin/show', 'Api\v1\ClientNextOfKinController@show');
        Route::get('client_next_of_kin/{id}/edit', 'Api\v1\ClientNextOfKinController@edit');
        Route::post('client_next_of_kin/{id}/update', 'Api\v1\ClientNextOfKinController@update');
        Route::get('client_next_of_kin/{id}/destroy', 'Api\v1\ClientNextOfKinController@destroy');
        //client files
        Route::get('{id}/file/create', 'Api\v1\ClientFileController@create');
        Route::post('{id}/file/store', 'Api\v1\ClientFileController@store');
        Route::get('{id}/file/show', 'Api\v1\ClientFileController@show');
        Route::get('file/{id}/edit', 'Api\v1\ClientFileController@edit');
        Route::post('file/{id}/update', 'Api\v1\ClientFileController@update');
        Route::get('file/{id}/destroy', 'Api\v1\ClientFileController@destroy');
        //titles
        Route::prefix('title')->group(function () {
            Route::get('/', 'Api\v1\TitleController@index');
            Route::get('get_titles', 'Api\v1\TitleController@get_titles');
            Route::get('create', 'Api\v1\TitleController@create');
            Route::post('store', 'Api\v1\TitleController@store');
            Route::get('{id}/show', 'Api\v1\TitleController@show');
            Route::get('{id}/edit', 'Api\v1\TitleController@edit');
            Route::post('{id}/update', 'Api\v1\TitleController@update');
            Route::get('{id}/destroy', 'Api\v1\TitleController@destroy');
        });
//client types
        Route::prefix('client_type')->group(function () {
            Route::get('/', 'Api\v1\ClientTypeController@index');
            Route::get('create', 'Api\v1\ClientTypeController@create');
            Route::post('store', 'Api\v1\ClientTypeController@store');
            Route::get('{id}/show', 'Api\v1\ClientTypeController@show');
            Route::get('{id}/edit', 'Api\v1\ClientTypeController@edit');
            Route::post('{id}/update', 'Api\v1\ClientTypeController@update');
            Route::get('{id}/destroy', 'Api\v1\ClientTypeController@destroy');
        });
//client relationship
        Route::prefix('client_relationship')->group(function () {
            Route::get('/', 'Api\v1\ClientRelationshipController@index');
            Route::get('get_client_relationships', 'Api\v1\ClientRelationshipController@get_client_relationships');
            Route::get('create', 'Api\v1\ClientRelationshipController@create');
            Route::post('store', 'Api\v1\ClientRelationshipController@store');
            Route::get('{id}/show', 'Api\v1\ClientRelationshipController@show');
            Route::get('{id}/edit', 'Api\v1\ClientRelationshipController@edit');
            Route::post('{id}/update', 'Api\v1\ClientRelationshipController@update');
            Route::get('{id}/destroy', 'Api\v1\ClientRelationshipController@destroy');
        });
        Route::prefix('client_identification_type')->group(function () {
            Route::get('/', 'Api\v1\ClientIdentificationTypeController@index');
            Route::get('get_client_identification_types', 'Api\v1\ClientIdentificationTypeController@get_client_identification_types');
            Route::get('create', 'Api\v1\ClientIdentificationTypeController@create');
            Route::post('store', 'Api\v1\ClientIdentificationTypeController@store');
            Route::get('{id}/show', 'Api\v1\ClientIdentificationTypeController@show');
            Route::get('{id}/edit', 'Api\v1\ClientIdentificationTypeController@edit');
            Route::post('{id}/update', 'Api\v1\ClientIdentificationTypeController@update');
            Route::get('{id}/destroy', 'Api\v1\ClientIdentificationTypeController@destroy');
        });
        Route::prefix('profession')->group(function () {
            Route::get('/', 'Api\v1\ProfessionController@index');
            Route::get('create', 'Api\v1\ProfessionController@create');
            Route::post('store', 'Api\v1\ProfessionController@store');
            Route::get('{id}/show', 'Api\v1\ProfessionController@show');
            Route::get('{id}/edit', 'Api\v1\ProfessionController@edit');
            Route::post('{id}/update', 'Api\v1\ProfessionController@update');
            Route::get('{id}/destroy', 'Api\v1\ProfessionController@destroy');
        });

    });
});
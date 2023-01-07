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

Route::prefix('client')->group(function () {
    Route::get('/', 'ClientController@index');
    Route::get('get_clients', 'ClientController@get_clients');
    Route::get('create', 'ClientController@create');
    Route::post('store', 'ClientController@store');
    Route::get('{id}/show', 'ClientController@show');
    Route::get('{id}/edit', 'ClientController@edit');
    Route::post('{id}/update', 'ClientController@update');
    Route::get('{id}/destroy', 'ClientController@destroy');
    Route::get('{id}/user/create', 'ClientController@create_user');
    Route::post('{id}/user/store', 'ClientController@store_user');
    Route::get('user/{id}/destroy', 'ClientController@destroy_user');
    Route::post('{id}/change_status', 'ClientController@change_status');
    //client identification
    Route::get(
        '{id}/client_identification/create',
        'ClientIdentificationController@create'
    );
    Route::post(
        '{id}/client_identification/store',
        'ClientIdentificationController@store'
    );
    Route::get(
        '{id}/client_identification/show',
        'ClientIdentificationController@show'
    );
    Route::get(
        'client_identification/{id}/edit',
        'ClientIdentificationController@edit'
    );
    Route::post(
        'client_identification/{id}/update',
        'ClientIdentificationController@update'
    );
    Route::get(
        'client_identification/{id}/destroy',
        'ClientIdentificationController@destroy'
    );
    //client next of kin
    Route::get(
        '{id}/client_next_of_kin/create',
        'ClientNextOfKinController@create'
    );
    Route::post(
        '{id}/client_next_of_kin/store',
        'ClientNextOfKinController@store'
    );
    Route::get(
        '{id}/client_next_of_kin/show',
        'ClientNextOfKinController@show'
    );
    Route::get(
        'client_next_of_kin/{id}/edit',
        'ClientNextOfKinController@edit'
    );
    Route::post(
        'client_next_of_kin/{id}/update',
        'ClientNextOfKinController@update'
    );
    Route::get(
        'client_next_of_kin/{id}/destroy',
        'ClientNextOfKinController@destroy'
    );
    //client files
    Route::get('{id}/file/create', 'ClientFileController@create');
    Route::post('{id}/file/store', 'ClientFileController@store');
    Route::get('{id}/file/show', 'ClientFileController@show');
    Route::get('file/{id}/edit', 'ClientFileController@edit');
    Route::post('file/{id}/update', 'ClientFileController@update');
    Route::get('file/{id}/destroy', 'ClientFileController@destroy');
    //titles
    Route::prefix('title')->group(function () {
        Route::get('/', 'TitleController@index');
        Route::get('get_titles', 'TitleController@get_titles');
        Route::get('create', 'TitleController@create');
        Route::post('store', 'TitleController@store');
        Route::get('{id}/show', 'TitleController@show');
        Route::get('{id}/edit', 'TitleController@edit');
        Route::post('{id}/update', 'TitleController@update');
        Route::get('{id}/destroy', 'TitleController@destroy');
    });
    //client types
    Route::prefix('client_type')->group(function () {
        Route::get('/', 'ClientTypeController@index');
        Route::get('get_client_types', 'ClientTypeController@get_client_types');
        Route::get('create', 'ClientTypeController@create');
        Route::post('store', 'ClientTypeController@store');
        Route::get('{id}/show', 'ClientTypeController@show');
        Route::get('{id}/edit', 'ClientTypeController@edit');
        Route::post('{id}/update', 'ClientTypeController@update');
        Route::get('{id}/destroy', 'ClientTypeController@destroy');
    });
    //client relationship
    Route::prefix('client_relationship')->group(function () {
        Route::get('/', 'ClientRelationshipController@index');
        Route::get(
            'get_client_relationships',
            'ClientRelationshipController@get_client_relationships'
        );
        Route::get('create', 'ClientRelationshipController@create');
        Route::post('store', 'ClientRelationshipController@store');
        Route::get('{id}/show', 'ClientRelationshipController@show');
        Route::get('{id}/edit', 'ClientRelationshipController@edit');
        Route::post('{id}/update', 'ClientRelationshipController@update');
        Route::get('{id}/destroy', 'ClientRelationshipController@destroy');
    });
    Route::prefix('client_identification_type')->group(function () {
        Route::get('/', 'ClientIdentificationTypeController@index');
        Route::get(
            'get_client_identification_types',
            'ClientIdentificationTypeController@get_client_identification_types'
        );
        Route::get('create', 'ClientIdentificationTypeController@create');
        Route::post('store', 'ClientIdentificationTypeController@store');
        Route::get('{id}/show', 'ClientIdentificationTypeController@show');
        Route::get('{id}/edit', 'ClientIdentificationTypeController@edit');
        Route::post('{id}/update', 'ClientIdentificationTypeController@update');
        Route::get(
            '{id}/destroy',
            'ClientIdentificationTypeController@destroy'
        );
    });
    Route::prefix('profession')->group(function () {
        Route::get('/', 'ProfessionController@index');
        Route::get('get_professions', 'ProfessionController@get_professions');
        Route::get('create', 'ProfessionController@create');
        Route::post('store', 'ProfessionController@store');
        Route::get('{id}/show', 'ProfessionController@show');
        Route::get('{id}/edit', 'ProfessionController@edit');
        Route::post('{id}/update', 'ProfessionController@update');
        Route::get('{id}/destroy', 'ProfessionController@destroy');
    });
});

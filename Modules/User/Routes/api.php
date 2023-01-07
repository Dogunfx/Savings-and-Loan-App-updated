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

    Route::post('login', 'Api\v1\AuthController@login');
    Route::post('register', 'Api\v1\AuthController@register');
    Route::middleware('auth:api')->post('/logout', 'Api\v1\AuthController@logout');
    Route::prefix('user')->group(function () {
        Route::get('/', 'Api\v1\UserController@index');
        Route::get('/get_custom_fields', 'Api\v1\UserController@get_custom_fields');
        Route::get('create', 'Api\v1\UserController@create');
        Route::post('store', 'Api\v1\UserController@store');
        Route::get('{id}/show', 'Api\v1\UserController@show');
        Route::get('{id}/edit', 'Api\v1\UserController@edit');
        Route::post('{id}/update', 'Api\v1\UserController@update');
        Route::get('{id}/destroy', 'Api\v1\UserController@destroy');
        Route::get('edit_profile', 'Api\v1\UserController@edit_profile');
        Route::post('update_profile', 'Api\v1\UserController@update_profile');
        //
        Route::prefix('role')->group(function () {
            Route::get('/', 'Api\v1\RoleController@index');
            Route::get('get_permissions', 'Api\v1\RoleController@get_permissions');
            Route::get('create', 'Api\v1\RoleController@create');
            Route::post('store', 'Api\v1\RoleController@store');
            Route::get('{id}/show', 'Api\v1\RoleController@show');
            Route::get('{id}/edit', 'Api\v1\RoleController@edit');
            Route::post('{id}/update', 'Api\v1\RoleController@update');
            Route::get('{id}/destroy', 'Api\v1\RoleController@destroy');
        });
    });
});
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

Route::middleware('auth:api')->get('/branch', function (Request $request) {
    return $request->user();
});
Route::prefix('v1/branch')->group(function() {
    Route::get('/', 'Api\v1\BranchController@index');
    Route::get('get_custom_fields', 'Api\v1\BranchController@get_custom_fields');
    Route::get('create', 'Api\v1\BranchController@create');
    Route::post('store', 'Api\v1\BranchController@store');
    Route::get('{id}/show', 'Api\v1\BranchController@show');
    Route::get('{id}/edit', 'Api\v1\BranchController@edit');
    Route::post('{id}/update', 'Api\v1\BranchController@update');
    Route::get('{id}/destroy', 'Api\v1\BranchController@destroy');
    Route::get('{id}/remove_user', 'Api\v1\BranchController@remove_user');
    Route::post('{id}/add_user', 'Api\v1\BranchController@add_user');
});
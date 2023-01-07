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

Route::prefix('branch')->group(function() {
    Route::get('/', 'BranchController@index');
    Route::get('get_branches', 'BranchController@get_branches');
    Route::get('create', 'BranchController@create');
    Route::post('store', 'BranchController@store');
    Route::get('{id}/show', 'BranchController@show');
    Route::get('{id}/edit', 'BranchController@edit');
    Route::post('{id}/update', 'BranchController@update');
    Route::get('{id}/destroy', 'BranchController@destroy');
    Route::get('{id}/remove_user', 'BranchController@remove_user');
    Route::post('{id}/add_user', 'BranchController@add_user');
});

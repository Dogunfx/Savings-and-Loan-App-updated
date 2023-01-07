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

Route::prefix('install')->group(function() {
    Route::get('/', 'InstallerController@index');
    Route::get('requirements', 'InstallerController@requirements');
    Route::get('permissions', 'InstallerController@permissions');
    Route::any('database', 'InstallerController@database');
    Route::any('installation', 'InstallerController@installation');
    Route::get('complete', 'InstallerController@complete');
});

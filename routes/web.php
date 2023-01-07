<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

////route for installation
//Route::get('install', 'InstallController@index');
//Route::group(['prefix' => 'install'], function () {
//    Route::get('start', 'InstallController@index');
//    Route::get('requirements', 'InstallController@requirements');
//    Route::get('permissions', 'InstallController@permissions');
//    Route::any('database', 'InstallController@database');
//    Route::any('installation', 'InstallController@installation');
//    Route::get('complete', 'InstallController@complete');
//
//});
////cron route
//Route::get('cron', 'CronController@index');
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return redirect('/');

});

//
//Route::get('update',
//    function () {
//        \Illuminate\Support\Facades\Artisan::call('migrate');
//        \Laracasts\Flash\Flash::success("Successfully Updated");
//        return redirect('/');
//    });
Route::get('migrate_seed',
    function () {
        \Illuminate\Support\Facades\Artisan::call('module:migrate');
        \Illuminate\Support\Facades\Artisan::call('module:seed');
        \Laracasts\Flash\Flash::success("Successfully Installed");
        return redirect('/');
    });
//Route::group(['prefix' => 'update'], function () {
//    Route::get('download', 'UpdateController@download');
//    Route::get('install', 'UpdateController@install');
//    Route::get('clean', 'UpdateController@clean');
//    Route::get('finish', 'UpdateController@finish');
//});

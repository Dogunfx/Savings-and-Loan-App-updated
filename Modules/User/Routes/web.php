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

Auth::routes();
Route::prefix('user')->group(function () {
    Route::get('/', 'UserController@index');
    Route::get('/get_users', 'UserController@get_users');
    Route::get('create', 'UserController@create');
    Route::post('store', 'UserController@store');
    Route::get('{id}/show', 'UserController@show');
    Route::get('{id}/edit', 'UserController@edit');
    Route::post('{id}/update', 'UserController@update');
    Route::get('{id}/destroy', 'UserController@destroy');
    Route::get('edit_profile', 'UserController@edit_profile');
    Route::post('update_profile', 'UserController@update_profile');
    Route::get('profile', 'UserController@profile');
    Route::post('profile/update_profile', 'UserController@update_profile');
    Route::get('profile/change_password', 'UserController@change_password');
    Route::post('profile/update_password', 'UserController@update_password');
    Route::get('profile/note', 'UserController@note');
    Route::get('profile/notification', 'UserController@notification');
    Route::get('profile/notification/mark_all_as_read', 'UserController@mark_all_notifications_as_read');
    Route::get('profile/notification/{id}/mark_as_read', 'UserController@mark_notification_as_read');
    Route::get('profile/notification/{id}/destroy', 'UserController@destroy_notification');
    Route::get('profile/notification/{id}/show', 'UserController@show_notification');
    Route::get('profile/activity_log/get_activity_logs', 'UserController@get_activity_logs');
    Route::get('profile/activity_log', 'UserController@activity_log');
    Route::get('profile/activity_log/{id}/show', 'UserController@show_activity_log');
    Route::get('profile/api', 'UserController@api');
    Route::post('profile/api/store_personal_access_token', 'UserController@store_personal_access_token');
    Route::post('profile/api/update_personal_access_token', 'UserController@update_personal_access_token');
    Route::get('profile/api/personal_access_token/{id}/destroy', 'UserController@destroy_personal_access_token');
    Route::post('profile/api/store_oauth_client', 'UserController@store_oauth_client');
    Route::post('profile/api/update_oauth_client', 'UserController@update_oauth_client');
    Route::get('profile/api/oauth_client/{id}/destroy', 'UserController@destroy_oauth_client');

    Route::get('profile/two_factor', 'UserController@two_factor');
    Route::post('profile/two_factor/enable', 'UserController@two_factor_enable');
    Route::post('profile/two_factor/disable', 'UserController@two_factor_disable');
    Route::post('profile/api/store_personal_access_token', 'UserController@store_personal_access_token');
    //
    Route::prefix('role')->group(function () {
        Route::get('/', 'RoleController@index');
        Route::get('/get_roles', 'RoleController@get_roles');
        Route::get('create', 'RoleController@create');
        Route::post('store', 'RoleController@store');
        Route::get('{id}/show', 'RoleController@show');
        Route::get('{id}/edit', 'RoleController@edit');
        Route::post('{id}/update', 'RoleController@update');
        Route::get('{id}/destroy', 'RoleController@destroy');
    });
});
Route::post('/2fa', function () {
    return redirect(URL()->previous());
})->name('2fa')->middleware('2fa');
//reports
Route::prefix('report')->group(function () {
    Route::get('user', 'ReportController@index');
    Route::get('user/performance', 'ReportController@performance');
});
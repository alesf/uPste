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

/*
 * Routes only unauthenticated users can access
 */
Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [
        'as' => 'index', 'uses' => 'IndexController@getIndex']);

    Route::get('login', [
        'as' => 'login', 'uses' => 'Auth\AuthController@getLogin']);

    Route::post('login', [
        'as' => 'login', 'uses' => 'Auth\AuthController@postLogin']);

    Route::get('register', [
        'as' => 'register', 'uses' => 'Auth\AuthController@getRegister']);

    Route::post('register', [
        'as' => 'register', 'uses' => 'Auth\AuthController@postRegister']);

    if (config('upste.require_email_verification')) {
        Route::get('register/verify/{confirmationCode}', [
            'as'   => 'register.confirmation', 'uses' => 'Auth\AuthController@confirm'
        ]);
    }

    Route::group(['prefix' => 'password'], function () {
        Route::get('email', [
            'as' => 'account.password.email', 'uses' => 'Auth\PasswordController@getEmail']);

        Route::post('email', [
            'as' => 'account.password.email', 'uses' => 'Auth\PasswordController@postEmail']);

        Route::get('reset/{token}', [
            'as' => 'account.password.reset', 'uses' => 'Auth\PasswordController@getReset']);

        Route::post('reset', [
            'as' => 'account.password.reset', 'uses' => 'Auth\PasswordController@postReset']);
    });
});

/*
 * Routes only authenticated users can access
 */
Route::group(['middleware' => 'auth', 'prefix' => 'u'], function () {
    Route::get('/', [
        'as' => 'account', 'uses' => 'Account\AccountController@getindex']);

    Route::get('logout', [
        'as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);

    Route::get('resources', [
        'as' => 'account.resources', 'uses' => 'Account\AccountController@getResources']);

    Route::get('resources/scripts/bash', [
        'as' => 'account.resources.bash', 'uses' => 'Account\AccountController@getBashScript']);

    Route::get('faq', [
        'as' => 'account.faq', 'uses' => 'Account\AccountController@getFaq']);

    Route::get('preferences', [
        'as' => 'account.preferences', 'uses' => 'Account\PreferencesController@get']);

    Route::post('preferences', [
        'as' => 'account.preferences', 'uses' => 'Account\PreferencesController@post']);

    Route::group(['prefix' => 'uploads'], function () {
        Route::get('/', [
            'as' => 'account.uploads', 'uses' => 'Account\AccountController@getUploads']);

        Route::post('{upload}/delete', [
            'as' => 'account.uploads.delete', 'uses' => 'Account\AccountController@deleteUpload']);

        Route::get('{upload}/delete', [
            'as' => 'account.uploads.delete', 'uses' => 'Account\AccountController@deleteUpload']);

        Route::get('{upload}/thumbnail', [
            'as' => 'account.uploads.thumbnail', 'uses' => 'Account\AccountController@getThumbnail']);
    });

    Route::post('resetkey', [
        'as' => 'account.resetkey', 'uses' => 'Account\AccountController@postResetKey']);
});

/*
 * Routes only admins can access
 */
Route::group(['middleware' => 'admin', 'prefix' => 'a'], function () {

    Route::get('/', [
        'as' => 'admin', 'uses' => 'Admin\AdminController@getIndex']);

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [
            'as' => 'admin.users', 'uses' => 'Admin\AdminController@getUsers']);

        Route::post('{user}/ban', [
            'as' => 'admin.users.ban', 'uses' => 'Admin\AdminController@postUserBan']);

        Route::post('{user}/unban', [
            'as' => 'admin.users.unban', 'uses' => 'Admin\AdminController@postUserUnban']);

        Route::get('{user}/uploads', [
            'as' => 'admin.users.uploads', 'uses' => 'Admin\AdminController@getUploads']);

        Route::post('{user}/delete', [
            'as' => 'admin.users.delete', 'uses' => 'Admin\AdminController@postUserDelete']);

        if (config('upste.require_user_approval')) {
            Route::get('requests', [
                'as' => 'admin.requests', 'uses' => 'Admin\AdminController@getRequests']);

            Route::post('{user}/accept', [
                'as' => 'admin.users.accept', 'uses' => 'Admin\AdminController@postUserAccept']);

            Route::post('{user}/reject', [
                'as' => 'admin.users.reject', 'uses' => 'Admin\AdminController@postUserReject']);
        }
    });
});

/*
 * API routes
 */
Route::group(['middleware' => 'api', 'prefix' => 'api'], function () {
    Route::post('upload', [
        'as' => 'api.upload', 'uses' => 'Api\UploadController@post']);
});

/*
 * Uploads
 */
Route::group(['domain' => config('upste.upload_domain')], function () {
    Route::get('{upload}', [
        'as' => 'files.get', 'uses' => 'DownloadController@get']);
});

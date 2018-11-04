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

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::post('settings/profile', 'Settings\ProfileController@updateProfileFoto');
    Route::patch('settings/password', 'Settings\PasswordController@update');

    Route::get('blacklist', 'BlackListController@index');
    Route::post('blacklist/saveUpdate', 'BlackListController@store');
    Route::post('blacklist/delete', 'BlackListController@destroy');
    Route::post('blacklist/deleteFoto', 'BlackListController@deleteFoto');

    Route::post('blacklist/saveLog', 'LogController@store');
    Route::get('getLogList', 'LogController@index');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{provider}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{provider}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

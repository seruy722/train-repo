<?php

use Illuminate\Http\Request;
use App\User;

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
        $user = $request->user();
        $userID = $user->id;
        $fullDataUser = User::findOrFail($userID);
        return $fullDataUser;
    });


    // PROFILE
    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::post('settings/profile', 'Settings\ProfileController@updateProfileFoto');
    // PASSWORD
    Route::patch('settings/password', 'Settings\PasswordController@update');
    // BLACKLIST
    Route::get('blacklist', 'BlackListController@index');
    Route::post('blacklist/saveUpdate', 'BlackListController@store');
    Route::post('blacklist/delete', 'BlackListController@destroy');
    Route::post('blacklist/deleteFoto', 'BlackListController@deleteFoto');
    Route::post('blacklist/saveLog', 'LogController@store');
    Route::get('getLogList', 'LogController@index');
    // USERS
    Route::get('users', 'UserController@index');
    Route::get('clientNames', 'UserController@getClientsNames');
    Route::post('users/saveUpdate', 'UserController@store');
    Route::post('users/delete', 'UserController@destroy');
    Route::post('users/deleteFoto', 'UserController@deleteFoto');
    // EMAILS
    Route::get('emails', 'EmailsController@index');
    Route::post('email/saveUpdate', 'EmailsController@store');
    Route::post('email/delete', 'EmailsController@destroy');
    // CARGOS
    Route::get('cargos', 'CargoController@index');
    Route::post('cargo/saveUpdate', 'CargoController@store');
    // DEBTS
    Route::get('debts', 'DebtsController@index');
//    Route::post('cargo/saveProfit', 'CargoController@store');
    // FAXES
    Route::get('faxes', 'FaxesController@index');
    Route::post('faxes/storeFax', 'FaxesMoreInfosController@store');
    Route::post('faxes/faxData', 'FaxesMoreInfosController@dataForFaxCounted');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{provider}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{provider}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

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


Route::group([
    'prefix' => 'auth',
    'namespace' => 'API'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::post('send_reset_otp', 'AuthController@send_reset_otp');
    Route::post('verify_otp', 'AuthController@verify_otp');
    Route::post('reset_password', 'AuthController@reset_password');

});

//Route::group([
//    'namespace' => 'API',
//    'middleware' => 'api',
//    'prefix' => 'password'
//], function () {
//    Route::post('request', 'PasswordResetController@request');
////    Route::get('find/{token}', 'PasswordResetController@find');
//    Route::post('reset', 'PasswordResetController@reset');
//});


Route::group([
    'middleware' => [
        'auth:api',
    ],
    'namespace' => 'API'
], function() {
    Route::get('auth/logout', 'AuthController@logout');
    Route::get('auth/user', 'AuthController@user');


});



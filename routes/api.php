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
    Route::post('auth/complete_profile', 'ProfileController@complete_profile');


    //check in
    Route::post('check_in', 'ProfileController@check_in');
    Route::get('check_in/history', 'ProfileController@check_in_history');


    //immunizations
    Route::get('immunizations', 'ImmunizationController@immunizations');
    Route::get('immunizations/facility/{id}', 'ImmunizationController@facility_immunizations');
    Route::get('immunizations/all', 'ImmunizationController@all_immunizations');
    Route::post('immunizations/new', 'ImmunizationController@new_immunization');

    //exposures
    Route::get('exposures', 'ExposureController@exposures');
    Route::get('exposures/all', 'ExposureController@all_exposures');
    Route::get('exposures/facility/{_id}', 'ExposureController@facility_exposures');
    Route::post('exposures/new', 'ExposureController@new_exposure');

    //feedback
    Route::post('feedback', 'ResourcesController@post_feedback');


    //collections
    Route::get('diseases', 'ResourcesController@diseases');
    Route::get('facilities', 'ResourcesController@facilities');
    Route::get('facility_departments/{id}', 'ResourcesController@facility_departments');
    Route::get('cadres', 'ResourcesController@cadres');
    Route::get('devices', 'ResourcesController@devices');

    //users
    Route::get('users', 'UserController@all_users');
    Route::get('hcw', 'UserController@all_hcw');
    Route::get('hcw/facility/{_id}', 'UserController@facility_hcw');

    //devices
    Route::get('devices/facility/{id}', 'DevicesController@facility_devices');
    Route::get('devices/all', 'DevicesController@all_devices');
    Route::post('devices/create', 'DevicesController@create_device');

    //resources
    Route::post('resources/cmes/create', 'ResourcesController@create_cme');
    Route::get('resources/cmes', 'ResourcesController@get_cmes');
    Route::post('resources/protocols/create', 'ResourcesController@create_protocol');
    Route::get('resources/protocols/{id}', 'ResourcesController@get_facility_protocols');
    Route::get('resources/hcw/protocols', 'ResourcesController@get_hcw_facility_protocols');







});



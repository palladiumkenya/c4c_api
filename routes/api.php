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

Route::group([
    'namespace' => 'API',
    'middleware' => 'api',
], function () {
    Route::get('resources/special', 'ResourcesController@get_special_resources');
    Route::get('resources/special/{id}', 'ResourcesController@get_special_resource');
});


Route::group([
    'middleware' => [
        'auth:api',
    ],
    'namespace' => 'API'
], function() {
    Route::get('auth/logout', 'AuthController@logout');
    Route::get('auth/user', 'AuthController@user');
    Route::post('auth/complete_profile', 'ProfileController@complete_profile');
    Route::post('auth/update_profile', 'ProfileController@update_profile');
    Route::post('auth/bulk/register', 'AuthController@upload_bulk_users');


    //check in
    Route::post('check_in', 'ProfileController@check_in');
    Route::get('check_in/history', 'ProfileController@check_in_history');
    Route::get('check_in/history/facility/{id}', 'ProfileController@check_in_history_by_facility');
    Route::post('check_in/approve', 'ProfileController@approve_check_in');



    //immunizations
    Route::get('immunizations', 'ImmunizationController@immunizations');
    Route::get('immunizations/facility/{id}', 'ImmunizationController@facility_immunizations');
    Route::get('immunizations/partner/{id}', 'ImmunizationController@partner_immunizations');
    Route::get('immunizations/facility/{id}/disease/{disease_id}', 'ImmunizationController@facility_immunizations_by_disease');
    Route::get('immunizations/partner/{id}/disease/{disease_id}', 'ImmunizationController@partner_immunizations_by_disease');
    Route::get('immunizations/all', 'ImmunizationController@all_immunizations');
    Route::get('immunizations/all/disease/{id}', 'ImmunizationController@all_immunizations_by_disease');
    Route::post('immunizations/new', 'ImmunizationController@new_immunization');

    //exposures
    Route::get('exposures', 'ExposureController@exposures');
    Route::get('exposures/all', 'ExposureController@all_exposures');
    Route::get('exposures/facility/{_id}', 'ExposureController@facility_exposures');
    Route::get('exposures/partner/{_id}', 'ExposureController@partner_exposures');
    Route::post('exposures/new', 'ExposureController@new_exposure');
    Route::post('exposures/covid/new', 'ExposureController@new_covid_exposure');
    Route::post('exposures/covid/new/ussd', 'ExposureController@new_ussd_covid_exposure');
    Route::get('exposures/covid/all', 'ExposureController@covid_exposures');
    Route::get('exposures/covid/facility/{_id}', 'ExposureController@facility_covid_exposures');
    Route::get('exposures/covid', 'ExposureController@my_covid_exposures');


    //feedback
    Route::post('feedback', 'ResourcesController@post_feedback');
    Route::get('feedback', 'ResourcesController@get_feedback');


    //collections
    Route::get('diseases', 'ResourcesController@diseases');
    Route::get('facilities', 'ResourcesController@facilities');
    Route::get('facilities/{id}', 'ResourcesController@partner_facilities');
    Route::get('facilities_paginated', 'ResourcesController@facilities_paginated');
    Route::post('facilities/department/add', 'ResourcesController@add_facility_department');
    Route::get('facility_departments/{id}', 'ResourcesController@facility_departments');
    Route::get('cadres', 'ResourcesController@cadres');
    Route::get('devices', 'ResourcesController@devices');
    Route::get('counties', 'ResourcesController@counties');
    Route::get('subcounties/{id}', 'ResourcesController@subcounties');

    //users
    Route::get('users', 'UserController@all_users');
    Route::get('hcw', 'UserController@all_hcw');
    Route::get('hcw/facility/{_id}', 'UserController@facility_hcw');
    Route::post('facility_admin/assign', 'UserController@assign_facility_admin');
    Route::get('facility_admin/{id}', 'UserController@get_facility_admin');
    Route::get('facility/admins/all', 'UserController@all_facility_admins');
    Route::get('hcw/partner/{_id}', 'UserController@get_partner_users');

    //devices
    Route::get('devices/facility/{id}', 'DevicesController@facility_devices');
    Route::get('devices/all', 'DevicesController@all_devices');
    Route::post('devices/create', 'DevicesController@create_device');

    //resources
    Route::post('resources/cmes/create', 'ResourcesController@create_cme');
    Route::post('resources/cmes/update', 'ResourcesController@update_cme');
    Route::get('resources/cmes', 'ResourcesController@get_cmes');
    Route::get('resources/cmes/{id}', 'ResourcesController@get_cme');
    Route::delete('resources/cmes/delete/{id}', 'ResourcesController@delete_cme');

    Route::post('resources/protocols/create', 'ResourcesController@create_protocol');
    Route::post('resources/protocols/update', 'ResourcesController@update_protocol');
    Route::get('resources/protocols/{id}', 'ResourcesController@get_facility_protocols');
    Route::get('resources/protocols/details/{id}', 'ResourcesController@get_protocols_details');
    Route::delete('resources/protocols/delete/{id}', 'ResourcesController@delete_facility_protocol');

    Route::get('resources/hcw/protocols', 'ResourcesController@get_hcw_facility_protocols');
    Route::get('resources/protocols/partner/{id}', 'ResourcesController@get_partner_protocols');


    Route::post('resources/special/create', 'ResourcesController@create_special_resource');
    Route::post('resources/special/update', 'ResourcesController@update_special_resource');

    Route::delete('resources/special/delete/{id}', 'ResourcesController@delete_special_resource');



    //broadcasts
    Route::post('broadcasts/web/create', 'BroadcastsController@create_web_broadcast');
    Route::post('broadcasts/web/direct', 'BroadcastsController@create_web_direct_broadcast');
    Route::get('broadcasts/web/history/{id}', 'BroadcastsController@get_facility_broadcast_history');
    Route::get('broadcasts/web/partner/history/{id}', 'BroadcastsController@get_partner_broadcast_history');
    Route::get('broadcasts/web/all', 'BroadcastsController@get_all_broadcast_history');


    Route::post('broadcasts/mobile/create', 'BroadcastsController@create_mobile_broadcast');
    Route::get('broadcasts/mobile/pending', 'BroadcastsController@pending_mobile_broadcasts');
    Route::get('broadcasts/mobile/approved', 'BroadcastsController@approved_mobile_broadcasts');
    Route::post('broadcasts/mobile/approve', 'BroadcastsController@approve_mobile_broadcast');


    //partners
    Route::get('partners', 'PartnerController@all_partners');



});



Route::post('broadcasts/nascop/create', 'API\BroadcastsController@create_nascop_broadcast');
Route::post('profiles/complete', 'API\ActionsController@complete_profiles');



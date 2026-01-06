<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    Route::crud('user', 'UserCrudController');
    Route::crud('booking', 'BookingCrudController');

    // ============ CUSTOM ROUTES FOR BOOKING MODULE ============

    // Order Verification Page (Pending SO/Order Requests)
    Route::get('booking/order-verification', 'BookingCrudController@orderVerification')
        ->name('booking.order-verification');

    // AJAX: Accept or Reject Order Request
    Route::post('booking/order-verify/{id}', 'BookingCrudController@orderVerify')
        ->name('booking.order-verify');

    // ============ AJAX ROUTES FOR DEPENDENT DROPDOWNS ============
    Route::get('/branchlocations/{bid}/', 'BookingCrudController@getBranchLocation')->name('get.branch');
    Route::get('/get-models/{segment_id}', 'BookingCrudController@getModels')->name('get.models');
    Route::get('/check-receipt/{rn}', 'BookingCrudController@CheckReceipt')->name('check-receipt');
    Route::get('/get-variants/{model}', 'BookingCrudController@getVariants')->name('get.variants');
    Route::get('/get-colors/{variant}', 'BookingCrudController@getColors')->name('get.colors');
    Route::get('/get-chassis-numbers/{modelCode}', 'BookingCrudController@getChassisNumbers')->name('get.chasis');
    Route::get('/get-accessories/{segment}/{model}/{variant}', 'BookingCrudController@getAccessories')->name('get.accessories');
    Route::get('/get-locations/{state_id}', 'BookingCrudController@getLocations')->name('get.locations');
    Route::get('/get-locations-by-pincode/{pincode}', 'BookingCrudController@getLocationsByPincode')->name('get.locations.by.pincode');
    Route::get('/get-state-by-location/{location_id}', 'BookingCrudController@getStateByLocation')->name('get.state.by.location');

    // ===========================================================

}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */

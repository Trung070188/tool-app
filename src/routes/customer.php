<?php
use Illuminate\Support\Facades\Route;



/**
 * Main customer routes
 */
Route::middleware(['auth:customer'])->namespace('Customer')->prefix('customer')->group(function () {
    Route::any('/dashboard/{action}', 'CustomerDashboardController');
    Route::any('/campaigns/{action}', 'CustomerCampaignsController');
});

Route::group(['prefix' => 'customer'], function(){
    Route::get('login','Auth\CustomerLoginController@showLoginForm')->name('customer.login');
    Route::post('login','Auth\CustomerLoginController@login');
    Route::get('logout','Auth\CustomerLoginController@logout');
});

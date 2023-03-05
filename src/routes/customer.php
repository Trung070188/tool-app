<?php
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');

/**
 * Main customer routes
 */
Route::middleware(['auth:customer'])->namespace('Customer')->prefix('customer')->group(function () {
    Route::any('/dashboard/{action}', 'CustomerDashboardController');
});

Route::group(['prefix' => 'customer'], function(){
    Route::get('login','Auth\CustomerLoginController@showLoginForm')->name('customer.login');
    Route::post('login','Auth\CustomerLoginController@login');
    Route::get('logout','Auth\CustomerLoginController@logout');
});

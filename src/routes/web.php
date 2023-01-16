<?php

use Illuminate\Support\Facades\Route;

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


Route::get('/ping', function() {
    return [
        'message' => 'PONG',
        'server_time' => date('Y-m-d H:i:s')
    ];
})->name('ping');



Route::get('/xadmin', function() {
    return redirect('/xadmin/dashboard/index');
});

Route::get('/customer', function() {
    return redirect('/customer/dashboard/index');
});


/**
 * Main admin routes
 */
Route::middleware(['auth:web'])->namespace('Admin')->prefix('xadmin')->group(function () {
    Route::any('/files/{action}', 'FilesController')->name('files');
    Route::get('/excel-sample', 'SampleController@excelSample')->name('excelSample');
    Route::any('/dashboard/{action}', 'DashboardController')->name('dashboard');
    $registry = require_once base_path('routes/registry.php');

    if (is_array($registry)) {
        foreach ($registry as $route) {
            Route::any($route['path'], $route['action'])->name($route['name']);
        }
    }
});

Route::group(['prefix' => 'xadmin'], function(){
    Route::get('login','Auth\LoginController@showLoginForm')->name('login');
    Route::post('login','Auth\LoginController@login');
    Route::get('logout','Auth\LoginController@logout');
});








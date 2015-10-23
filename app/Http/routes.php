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

Route::get('/', function () {
    return view('welcome');
});
Route::model('reports','App\Bi_Report');
Route::resource('reports','ReportsController');
Route::post('reports/getdata','ReportsController@GetData');
Route::post('reports/getfilters','ReportsController@GetFilters');
Route::post('reports/processfilters','ReportsController@ProcessFilters');
Route::post('reports/clearfilters','ReportsController@ClearFilters');

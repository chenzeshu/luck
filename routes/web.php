<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ema/{date}', 'MACDController@ema');
Route::get('/diff/{code}', 'MACDController@diff');

Route::get('dea',  'MACDController@dea');


Route::get('/chart', 'MACDController@chart');
Route::get('/sohu', 'SohuController@index');
Route::get('/getx/{code}', 'SohuController@getx');


Route::get('/getCode', 'MACDController@getcode');
Route::get('/test', 'MACDController@test');

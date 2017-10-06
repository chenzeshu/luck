<?php

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



Route::group(['prefix'=>'v1', 'namespace'=> "v1"], function (){
    Route::group(['prefix'=>'stock'], function (){
        Route::get('/index', 'StockController@index');
        Route::get('/getstock', 'StockController@getStock');
        Route::get('/search/{code}', 'StockController@search');
        Route::get('/showChoice/{stock_id}', 'StockController@showChoice');
    });

    Route::group(['prefix'=>'x'], function (){
        Route::get('/getx', 'XController@getx');
        Route::get('/day', 'XController@day');
        Route::get('/week', 'XController@week');
        Route::get('/month', 'XController@month');
    });

    Route::group(['prefix'=>'d', 'namespace'=>'X'], function (){
        Route::get('/getx/{code}/{start}/{end}', 'DayController@getX');
    });

    Route::group(['prefix'=>'w', 'namespace'=>'X'], function (){
        Route::get('/getx/{code}/{start}/{end}', 'WeekController@getX');
    });

    Route::group(['prefix'=>'m', 'namespace'=>'X'], function (){
        Route::get('/getx/{code}/{start}/{end}', 'MonthController@getX');
    });

    Route::group(['prefix'=>'f'], function (){
        Route::get('/getdata/{page}/{size}','favController@getData');
        Route::post('/save','favController@save');
        Route::get('/delete/{id}','favController@delete');
    });
});
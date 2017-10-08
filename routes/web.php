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
    //股票列表
    Route::group(['prefix'=>'stock'], function (){
        Route::get('/index', 'StockController@index');
        Route::get('/getstock', 'StockController@getStock');
        Route::get('/search/{code}', 'StockController@search');
        Route::get('/showChoice/{stock_id}', 'StockController@showChoice');
    });

    //得到数据的job
    Route::group(['prefix'=>'x'], function (){
        Route::get('/getx', 'XController@getx');
        Route::get('/day', 'XController@day');
        Route::get('/week', 'XController@week');
        Route::get('/month', 'XController@month');
    });

    //日数据
    Route::group(['prefix'=>'d', 'namespace'=>'X'], function (){
        Route::get('/getx/{code}/{start}/{end}', 'DayController@getX');
    });
    //周数据
    Route::group(['prefix'=>'w', 'namespace'=>'X'], function (){
        Route::get('/getx/{code}/{start}/{end}', 'WeekController@getX');
    });
    //月数据
    Route::group(['prefix'=>'m', 'namespace'=>'X'], function (){
        Route::get('/getx/{code}/{start}/{end}', 'MonthController@getX');
    });

    //收藏
    Route::group(['prefix'=>'f'], function (){
        Route::get('/getdata/{page}/{size}','favController@getData');
        Route::post('/save','favController@save');
        Route::get('/delete/{id}','favController@delete');
    });

    //消息
    Route::group(['prefix'=>'msg'], function (){
        Route::get('/read/{page}/{size}','MsgController@read');
        Route::get('/unread/{page}/{size}','MsgController@unread');
        Route::get('/change/{id}','MsgController@change');
        Route::get('/delete/{id}','MsgController@delete');
    });

    Route::group(['prefix'=>'rec', 'namespace'=>'Rec'], function(){
        //月筛选
        Route::get('/getmonth/{page}/{size}','MController@getData');
        Route::any('/msearch/{page}/{size}','MController@search');
        //周筛选
        Route::get('/getweek/{page}/{size}','WController@getData');
        Route::any('/wsearch/{page}/{size}','WController@search');

        //复合筛选
        Route::get('/getmul/{page}/{size}','RecController@getData');
        Route::any('/mulsearch/{page}/{size}','RecController@search');
    });

    //提醒策略
    Route::group(['prefix'=>'s', 'namespace'=>'Strategy'], function(){
        //按macd值
        Route::get('/v/delete/{id}','ValueController@delete');
        Route::get('/v/known/{id}','ValueController@known');
        Route::get('/v/{page}/{size}','ValueController@getData');
        Route::post('/v/create','ValueController@create');
        Route::post('/v/update','ValueController@update');


        //按时间
        Route::get('/t/delete/{id}','TimeController@delete');
        Route::get('/t/known/{id}','TimeController@known');
        Route::get('/t/{page}/{size}','TimeController@getData');
        Route::post('/t/create','TimeController@create');
        Route::post('/t/update','TimeController@update');
    });
});
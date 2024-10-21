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

Route::get('/artisan', function(){
    \Artisan::call('schedule:run');
});


Route::get('/pay', 'WebController@init_pay');
Route::get('/pay/result', 'WebController@complete_pay')->name('web.pay-success');
Route::get('/', 'WebController@index')->name('web.web.index');

//Route::get('/images/{id}', 'WebController@propertyImages')->name('web.property.images');
//Route::get('/{slug}', 'RequestController@request')->name('web.request.view');
//Route::get('/{slug}/{id?}', 'RequestController@requestProperty')->name('web.request.property');

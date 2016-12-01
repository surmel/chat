<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/', 'WelcomeController@index');
Route::get('/welcome', 'WelcomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::post('/addRoom', 'WelcomeController@addRoom');
Route::post('/deleteRoom', 'WelcomeController@deleteRoom');
Route::post('/messages', 'WelcomeController@messages');
Route::post('/addMessage', 'WelcomeController@addMessage');
Route::post('/updateMessages', 'WelcomeController@updateMessages');
Route::post('/signAsGuest', 'WelcomeController@signAsGuest');
Route::post('/generateLink', 'WelcomeController@generateLink');
Route::get('/{link}', 'WelcomeController@index');
Route::post('/getLink', 'WelcomeController@getLink');


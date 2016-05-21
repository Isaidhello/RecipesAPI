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

/** User Routes */
Route::post('/user/register', ['uses' => 'UserController@registerUser']);
Route::post('/user/login', ['uses' => 'UserController@loginUser']);

/** Search Routes */
Route::get('/search/{term}', ['uses' => 'SearchController@searchByTerm']);

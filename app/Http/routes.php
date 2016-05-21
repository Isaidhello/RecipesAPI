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
Route::get('/report/{food_id}', ['uses' => 'SearchController@getFoodDetail']);

/**
 * This route is necessary due a Laravel issue with Payload on PUT Method Verb
 * https://github.com/laravel/framework/issues/5503
 */
Route::post('/recipes/{id}', ['uses' => 'RecipesController@updateRecipe']);

/** Recipes routes */
Route::resource('/recipes', 'RecipesController');

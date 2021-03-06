<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('/v1/')->group(function() {
  Route::middleware('auth:api')->get('/user', 'ProfilesApiController@authRouteAPI');
  Route::name('api.')->group(function() {
      Route::name('index')->get('/', 'ProfilesApiController@index');
      Route::name('show')->get('/{profile}', 'ProfilesApiController@show');
  });
});

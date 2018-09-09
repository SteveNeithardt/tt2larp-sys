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

// api for all stations calling for their data
Route::get('/station', 'StationApiController@index')->name('station api');

// command center routes
Route::get('/station/list', 'StationController@getSimpleList')->name('command get stations');

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

Route::get('/station/{id}/window_info', 'StationController@window_info')->name('station window info');
Route::post('/station/{id}/action', 'StationController@action')->name('station action');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

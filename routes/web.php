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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::get('/ability', 'AbilityController@portal')->name('ability portal');
Route::get('/ability/list', 'AbilityController@getList')->name('get abilities');
Route::post('/ability/store', 'AbilityController@store')->name('store ability');

Route::get('/character', 'CharacterController@portal')->name('list characters');
Route::get('/character/list', 'CharacterController@getList')->name('get characters');
Route::post('/character/store', 'CharacterController@store')->name('store character');


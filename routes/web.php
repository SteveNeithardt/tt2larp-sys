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

Route::get('/ability/list', 'AbilityController@index')->name('list abilities');
Route::get('/ability/{id}', 'AbilityController@view')->name('view ability');
Route::get('/ability/{id}/edit', 'AbilityController@edit')->name('edit ability');
Route::post('/ability/{id}/edit', 'AbilityController@store')->name('store ability');

Route::get('/character/list', 'CharacterController@index')->name('list characters');
Route::get('/character/{id}', 'CharacterController@view')->name('view character');
Route::get('/character/{id}/edit', 'CharacterController@edit')->name('edit character');
Route::post('/character/{id}/edit', 'CharacterController@store')->name('store character');


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
Route::post('/ability/store', 'AbilityController@store')->name('store ability');

Route::get('/character/list', 'CharacterController@index')->name('list characters');
Route::get('/character/{id}', 'CharacterController@view')->where(['id' => '[0-9]+'])->name('view character');
Route::get('/character/edit/{id?}', 'CharacterController@edit')->where(['id' => '[0-9]+'])->name('edit character');
Route::post('/character/store', 'CharacterController@store')->name('store character');


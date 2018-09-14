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

//Auth::routes();
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/command', 'HomeController@command')->name('command');
Route::get('/communication', 'ChatController@index')->name('chat index');

// chat routes for the communications post
Route::get('/chat', 'ChatController@messages')->name('get chat list');
Route::post('/chat', 'ChatController@newMessage')->name('new chat message');

Route::group([ 'middleware' => 'auth' ], function() {
	Route::get('/', 'HomeController@index')->name('home');

	Route::get('/ability', 'AbilityController@portal')->name('ability portal');
	Route::get('/ability/list', 'AbilityController@getList')->name('get abilities');
	Route::post('/ability/store', 'AbilityController@storeAbility')->name('store ability');
	Route::post('/ability/delete', 'AbilityController@deleteAbility')->name('delete ability');

	Route::get('/character', 'CharacterController@portal')->name('character portal');
	Route::get('/character/list', 'CharacterController@getList')->name('get characters');
	Route::post('/character/store', 'CharacterController@storeCharacter')->name('store character');
	Route::post('/character/delete', 'CharacterController@deleteCharacter')->name('delete character');

	Route::get('/problem', 'ProblemController@portal')->name('problem portal');
	Route::get('/problem/list', 'ProblemController@getList')->name('get problems');
	Route::post('/problem/store', 'ProblemController@storeProblem')->name('store problem');
	Route::post('/problem/delete', 'ProblemController@deleteProblem')->name('delete problem');
	Route::get('/problem/{problem_id}/list', 'ProblemController@getStepList')->name('get steps');
	Route::post('/problem/{problem_id}/node/store', 'ProblemController@storeNode')->name('store node');
	Route::post('/problem/{problem_id}/node/delete', 'ProblemController@deleteNode')->name('delete node');
	Route::post('/problem/{problem_id}/edge/store', 'ProblemController@storeEdge')->name('store edge');
	Route::post('/problem/{problem_id}/edge/delete', 'ProblemController@deleteEdge')->name('delete edge');

	Route::get('/library', 'LibraryController@portal')->name('library portal');
	Route::get('/library/list', 'LibraryController@getList')->name('get articles');
	Route::post('/library/store', 'LibraryController@storeArticle')->name('store article');
	Route::post('/library/delete', 'LibraryController@deleteArticle')->name('delete article');
	Route::get('/library/{article_id}/list', 'LibraryController@getPartList')->name('get parts');
	Route::post('/library/{article_id}/part/store', 'LibraryController@storePart')->name('store part');
	Route::post('/library/{article_id}/part/delete', 'LibraryController@deletePart')->name('delete part');

	Route::get('/station', 'StationController@portal')->name('station portal');
	Route::get('/station/list', 'StationController@getList')->name('get stations');
	Route::get('/station/libraries', 'StationController@getLibraryList')->name('get library stations');
	Route::post('/station/names', 'StationController@setNames')->name('set station names');
	Route::post('/station/problem', 'StationController@setActiveProblem')->name('set station active problem');
	Route::post('/station/step', 'StationController@setActiveStep')->name('set station active step');
	Route::get('/station/step', 'StationController@getStepEntourage')->name('get station active step entourage');

	Route::get('/crafting', 'CraftingController@portal')->name('crafting portal');
	Route::get('/crafting/list', 'CraftingController@getList')->name('get recipes');
	Route::post('/crafting/recipe/store', 'CraftingController@storeRecipe')->name('store recipe');
	Route::post('/crafting/recipe/delete', 'CraftingController@deleteRecipe')->name('delete recipe');

	// authenticated only unread chat stuffs
	Route::get('/chat/unread', 'ChatController@unread')->name('get chat unread');
	Route::post('/chat/unread', 'ChatController@markRead')->name('mark chat read');
});

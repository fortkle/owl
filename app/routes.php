<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
 */

/*
 * Dont Need Login.
 */
Route::get('login', array('uses' => 'LoginController@login'));
Route::post('login', array('uses' => 'LoginController@auth'));
Route::get('signup', array('uses' => 'SignupController@signup'));
Route::post('signup', array('uses' => 'SignupController@register'));
Route::get('search', array('uses' => 'SearchController@index'));
Route::get('search/json', array('uses' => 'SearchController@json'));
Route::get('search/jsonp', array('uses' => 'SearchController@jsonp'));
Route::get('tags/suggest', array('uses' => 'TagController@suggest'));

/*
 * Need Login.
 */
Route::group(array('before' => 'sentry'), function() {
    // Basic
    Route::get('/', array('uses' => 'IndexController@index'));
    Route::get('logout', array('uses' => 'LogoutController@logout'));

    // Items
    Route::get('items', array('as' => 'items.index', 'uses' => 'ItemController@index'));
    Route::get('items/create', array('as' => 'items.create', 'uses' => 'ItemController@create'));
    Route::post('items', array('as' => 'items.store', 'uses' => 'ItemController@store'));
    Route::get('items/{items}/edit', array('as' => 'items.edit', 'uses' => 'ItemController@edit'));
    Route::get('items/{items}/history', array('as' => 'items.history', 'uses' => 'ItemController@history'));
    Route::put('items/{items}', array('as' => 'items.update', 'uses' => 'ItemController@update'));
    Route::delete('items/{items}', array('as' => 'items.destroy', 'uses' => 'ItemController@destroy'));
    Route::resource('templates', 'TemplateController');
    Route::resource('stocks/search', 'StockController@search');
    Route::resource('stocks', 'StockController');
    Route::resource('likes', 'LikeController');

    // Tags
    Route::get('tags/{tags}', array('as' => 'tags.show', 'uses' => 'TagController@show'));

    // Users
    Route::get('user/edit', array('uses' => 'UserController@edit'));
    Route::put('user/edit', array('uses' => 'UserController@update'));
    Route::get('user/stock', array('uses' => 'UserController@stock'));
    Route::post('user/password', array('uses' => 'UserController@reset'));
    Route::get('/{username}', array('uses' => 'UserController@show'));

    Route::post('image/upload', array('uses' => 'ImageController@upload'));
    Route::post('comment/create', array('uses' => 'CommentController@create'));
    Route::post('comment/destroy', array('uses' => 'CommentController@destroy'));
    Route::post('comment/update', array('uses' => 'CommentController@update'));
});

/*
 * Dont Need Login.（must write after items/***)
 */
Route::get('items/{items}', array('as' => 'items.show', 'uses' => 'ItemController@show'));


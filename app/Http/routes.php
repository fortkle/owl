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

/*
 * Not Logged In.
 */
Route::group(['middleware' => 'notLogin'], function () {
    Route::get('login', array('uses' => 'AuthController@login'));
    Route::post('login', array('uses' => 'AuthController@attempt'));
    Route::get('signup', array('uses' => 'UserController@signup'));
    Route::post('signup', array('uses' => 'UserController@register'));
    Route::get('password/reminder', array('uses' => 'ReminderController@remind'));
    Route::post('password/reminder', array('uses' => 'ReminderController@send'));
    Route::get('password/reset/{token}', array('uses' => 'ReminderController@edit'));
    Route::post('password/reset', array('uses' => 'ReminderController@update'));
});

/*
 * Dont Need Login.
 */
Route::get('search', array('uses' => 'SearchController@index'));
Route::get('search/json', array('uses' => 'SearchController@json'));
Route::get('search/jsonp', array('uses' => 'SearchController@jsonp'));
Route::get('tags/suggest', array('uses' => 'TagController@suggest'));

/*
 * Need Login and Role is Owner
 */
Route::group(['middleware' => ['login','owner']], function () {
    Route::get('manage', array('uses' => 'AdminController@index'));
    Route::get('manage/user/index', array('uses' => 'UserController@index'));
    Route::post('manage/user/{user_id}/roleUpdate', array('uses' => 'UserController@roleUpdate'));
    Route::get('manage/flow/index', array('uses' => 'FlowTagController@index'));
    Route::post('manage/flow/store', array('uses' => 'FlowTagController@store'));
    Route::delete('manage/flow/destroy', array('as' => 'flow.destroy', 'uses' => 'FlowTagController@destroy'));
});

/*
 * Need Login.
 */
Route::group(['middleware' => 'login'], function () {
    // Basic
    Route::get('/', array('uses' => 'IndexController@index'));
    Route::get('logout', array('uses' => 'AuthController@logout'));

    // Items
    Route::get('items', array('as' => 'items.index', 'uses' => 'ItemController@index'));
    Route::get('items/create', array('as' => 'items.create', 'uses' => 'ItemController@create'));
    Route::post('items', array('as' => 'items.store', 'uses' => 'ItemController@store'));
    Route::get('items/{items}/edit', array('as' => 'items.edit', 'uses' => 'ItemController@edit'));
    Route::get('items/{items}/history', array('as' => 'items.history', 'uses' => 'ItemController@history'));
    Route::put('items/{items}', array('as' => 'items.update', 'uses' => 'ItemController@update'));
    Route::delete('items/{items}', array('as' => 'items.destroy', 'uses' => 'ItemController@destroy'));
    Route::post('items/parse', array('as' => 'items.parse', 'uses' => 'ItemController@parse'));
    Route::resource('templates', 'TemplateController');
    Route::resource('favorites', 'StockController');
    Route::resource('likes', 'LikeController');

    // Tags
    Route::get('tags', array('as' => 'tags.index', 'uses' => 'TagController@index'));
    Route::get('tags/{tags}', array('as' => 'tags.show', 'uses' => 'TagController@show'));

    // Users
    Route::get('user/edit', array('uses' => 'UserController@edit'));
    Route::put('user/edit', array('uses' => 'UserController@update'));
    Route::get('user/stock', array('uses' => 'UserController@stock'));
    Route::post('user/password', array('uses' => 'UserController@password'));
    Route::get('/{username}', array('uses' => 'UserController@show'));

    // User Roles
    Route::get('user/role/initial', array('uses' => 'UserRoleController@initial'));
    Route::post('user/role/initial', array('uses' => 'UserRoleController@initialRegister'));

    Route::post('image/upload', array('uses' => 'ImageController@upload'));
    Route::post('comment/create', array('uses' => 'CommentController@create'));
    Route::post('comment/destroy', array('uses' => 'CommentController@destroy'));
    Route::post('comment/update', array('uses' => 'CommentController@update'));

    // User Mail Notification Settings
    Route::post('user/notification/update', ['as' => 'user.notification.update', 'uses' => 'MailNotifyController@update']);
});

/*
 * Dont Need Login. (must write after items/***)
 */
Route::get('items/{items}', array('as' => 'items.show', 'uses' => 'ItemController@show'));

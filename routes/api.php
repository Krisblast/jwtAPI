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





Route::get('/sub/{id?}', 'SubController@index');
Route::get('/v1/employees/{id?}', 'Employees@index');
Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\LoginController@register');
Route::get('/thread/{id?}', 'ThreadController@index');
Route::get('/thread/{thread_id}/detail', 'ThreadController@getThreadDetail');
Route::get('/user/public/{id}', 'PublicUserController@index');
Route::get('/user/public/{id}/threads', 'PublicUserController@getUserThreads');

Route::get('/comment/{type}/{id}', 'CommentController@index'); //Type - user, sub, thread

Route::group([
    'prefix' => 'restricted',
    'middleware' => 'auth:api',

], function () {

    // Authentication Routes...
    Route::get('logout', 'Auth\LoginController@logout');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/employees', 'Employees@store');
    Route::put('/employees/{id}', 'Employees@update');
    Route::delete('/employees/{id}', 'Employees@destroy');


    Route::post('/sub', 'SubController@store');
    Route::put('/sub/{id}', 'SubController@update');
    Route::delete('/sub/{id}', 'SubController@destroy');


    Route::post('/subscribe', 'SubscriberController@store');
    Route::delete('/subscribe/{id}', 'SubscriberController@destroy');
    Route::get('/subscribe', 'SubscriberController@index');

    Route::get('/sub/subscribed', 'SubController@getAllSubscribed');


    Route::post('/thread', 'ThreadController@store');
    Route::post('/comment', 'CommentController@store');



    Route::post('/vote/up', 'VoteController@postUpVote');
    Route::post('/vote/down ', 'VoteController@postDownVote');



});
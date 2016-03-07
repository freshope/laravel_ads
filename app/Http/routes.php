<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
});

Route::get('image', function() {
    // set cookie
    //Cookie::queue('ctime', date('Y-m-d H:i:s'), 60);
    setcookie('ctime', date('Y-m-d H:i:s'));
    
    //return 'set cookie';
    return redirect('c9-logo.png');
});


Route::get('facebook/api/{version}/{all}', 'FacebookController@bridgeApi')->where('all', '.*');
Route::controller('facebook', 'FacebookController');


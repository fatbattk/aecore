<?php

<<<<<<< HEAD
// landing page
Route::get('/', function() {
  return 'Welcome to Aecore Alpha! <a href="/login">Login</a>';
});

// Sign up
Route::get('signup', array('uses' => 'UsersController@showSignup'));
Route::post('signup', array('uses' => 'UsersController@doSignup'));

// Log in
Route::get('login', 'UsersController@showLogin');
Route::post('login', 'UsersController@doLogin');

// Log out
Route::get('logout', array('uses' => 'UsersController@doLogout'));

// Reset password
Route::get('reset', array('uses' => 'UsersController@showReset'));

/*
=======
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

Route::get('/', 'WelcomeController@index');
>>>>>>> c30b6609d3e2c78ca3d66f166c1505caccb22195

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
<<<<<<< HEAD
*/


=======
>>>>>>> c30b6609d3e2c78ca3d66f166c1505caccb22195

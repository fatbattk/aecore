<?php

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
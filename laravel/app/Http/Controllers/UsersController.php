<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use Hash;
use DB;

class UsersController extends Controller {

  public function showLogin() {
    return view('auth.login');
  }

  public function doLogin() {
    
    // validate the info, create rules for the inputs
    $rules = array(
      'email' => 'required|email', // make sure the email is an actual email
      'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
    );

    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);

    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('login')
        ->withErrors($validator) // send back all errors to the login form
        ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
    } else {
      // create our user data for the authentication
      $userdata = array(
        'email'     => Input::get('email'),
        'password'  => Input::get('password')
      );
      
      // get data for user
      $user = DB::table('users')
              ->where('email', '=', Input::get('email'))->first();
           
      // check if account is active
      if($user != null && $user->status == 'active') {
        if(Auth::attempt($userdata)) {
          
          if(Auth::user()->company['id'] != null) {
            Session::put('company_id', Auth::user()->company['id']);
            Session::put('company_name', Auth::user()->company['name']);
            Session::put('company_join_type', Auth::user()->company_join_type);
          }
          
          return Redirect::to('projects');
        } else {
          // validation not successful, send back to form
          return Redirect::to('login')
            ->with('dangerMessage', 'Incorrect email & password combination.');          
        }
      } else {      
        // user is not active or does not exist
        return Redirect::to('login')
          ->with('warningMessage', 'No active account found. <a href="/signup" class="bold">Sign Up Here</a>.');
      }
    }
  }

  public function doLogout() {
    Auth::logout(); // log the user out of our application
    Session::flush();
    return Redirect::to('login'); // redirect the user to the login screen
  }
  
  public function showSignup() {
    return view('auth.signup');
  }
  
  public function doSignup() {
    // validate the info, create rules for the inputs
    $rules = array(
      'name' => 'required',
      'username' => 'required|unique:users,username',
      'email' => 'required|email',
      'password' => 'required|alphaNum|min:6' // password can only be alphanumeric and has to be greater than 3 characters
    );
    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);

    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('signup')
        ->withErrors($validator) // send back all errors to the login form
        ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
    } else {
      //check if email exists and account = static || diabled, then activate
      $user = DB::table('users')
              ->where('email', '=', Input::get('email'))->first();
      if($user != null && $user->status == 'active') {
        // active account already exists
        return Redirect::to('signup')
          ->with('signupFailed', 'This email address is being used by an active account. Did you <a href="/reset">forget your password</a>?');
      } else {
        // no active account, either update status or create new
        $userdata = array(
          'identifier' => FunctionsController::RandomString('10'),
          'email' => Input::get('email'),
          'password' => Hash::make(Input::get('password')),
          'name' => Input::get('name'),
          'username' => Input::get('username'),
          'status' => 'active'
        );
        
        if($user != null && ($user->status == 'disabled' || $user->status == 'static')) {
          // update user account to active
          $user_update = User::find($user->id);
          $user_update->password = $userdata['password'];
          $user_update->name = $userdata['name'];
          $user_update->username = $userdata['username'];
          $user_update->status = 'active';
          $user_update->save();
          
          // login & head to projects page
          Auth::loginUsingId($user_update->id);
          
          // Update projectuser status to active (for invited)
          Projectuser::where('user_id', '=', Auth::User()->id)
                ->where('status', '=', 'invited')
                ->update(['status'=>'active']);
          
          return Redirect::to('projects');
          
        } else {
          // create new user
          $newUser = User::create($userdata);
  
          // if new user, login and add defaul data
          if($newUser){
            // sign in
            Auth::login($newUser);
  
            // add blank entry in userphones table
            $mobile = array (
              'direct' => '',
              'mobile' => ''
            );
            $newUser->userphone()->create($mobile);
  
            // add blank entry in useravatars table
            $avatar = array (
              'file_id_lg' => '',
              'file_id_sm' => ''
            );
            $newUser->useravatar()->create($avatar);
            
            // head to projects page
            return Redirect::to('projects');
          } else {
            // validation not successful, send back to form
            return Redirect::to('signup')
              ->with('signupFailed', 'Error, new account not created.');
          }
        }
      }
    }
  }
   
  public function showReset() {
    return view('auth.reset');
  }
  
}

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
  use View;
  use DB;
  use Hash;
  
class SettingsController extends Controller {

  public function showProfile() {
    return view('settings.personal.profile');
  }

  public function updateProfile() {
    
    // validate the info, create rules for the inputs
    $rules = array(
      'name' => 'required',
      'username' => 'required|unique:users,username,'.Auth::User()->id
    );

    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);

    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('settings/profile')
        ->withErrors($validator); // send back all errors to the login form
    } else {
            
      // Get form data
      $user_data = array (
        'name' => Input::get('name'),
        'username' => Input::get('username'),
        'title' => Input::get('title'),
        'timezone' => Input::get('timezone')
      );
      Auth::user() -> update($user_data);
      
      $mobile = array (
        'direct' => Input::get('direct'),
        'mobile' => Input::get('mobile')
      );
      Auth::user()->userphone()->update($mobile);
              
      return Redirect::to('settings/profile')
              ->with('UpdateSuccess', '<strong>Success!</strong> Your profile information has been updated.');
    }
  }
  
  public function showAvatarCropModal($type) {
    // Return to the modal view
    return view('settings.modals.crop')
            ->with('type', $type);
  }
  
  public function showAccount() {
    $companydata = DB::table('companys')
              ->leftjoin('companylocations', 'companys.id', '=', 'companylocations.company_id')
              ->leftjoin('companywebsites', 'companys.id', '=', 'companywebsites.company_id')
              ->where('companys.id', '=', Auth::user()->company['id'])
              ->first();
    
    return view('settings.personal.account')
            ->with('companydata', $companydata);
  }
  
  public function changePassword() {
    
    // validate the info, create rules for the inputs
    $rules = array(
      'old_password' => 'required',
      'new_password' => 'required|min:6',
      'confirm_new_password' => 'required|same:new_password'
    );
    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);
    
    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('settings/account')
        ->withErrors($validator); // send back all errors to the login form
    } else {
      
      $user = User::find(Auth::user()->id);
      $old_password = Input::get('old_password');
      $new_password = Input::get('new_password');
      
      if(Hash::check($old_password, $user->getAuthPassword())) {
        // Password provided ok
        $user->password = Hash::make($new_password);
        if($user->save()) {
          return Redirect::to('settings/account')
              ->with('accountUpdateSuccess', '<strong>Success!</strong> Your password has been updated.');
        } else {
          return Redirect::to('settings/account')
              ->with('accountUpdateError', '<strong>Error!</strong> Your password could not be updated.');
        }
      } else {
        return Redirect::to('settings/account')
            ->with('accountUpdateError', '<strong>Error!</strong> The old password you provided is incorrect.');
      }
    }
  }
  
  public function createCompanyForm() {
    return view('settings.company.company_create');
  }
  
  public function createCompany() {
    // validate the info, create rules for the inputs
    $rules = array(
      'name' => 'required',
      'type' => 'required',
      'labor' => 'required'
    );
    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);
    
    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('settings/account')
        ->withErrors($validator); // send back all errors to the login form
    } else {
      
      // Get post form data
      $company_data = array (
        'name' => Input::get('name'),
        'type' => Input::get('type'),
        'labor' => Input::get('labor')
      );
      $company = new Company;
      $company_insert_id = $company -> create($company_data)->id;
      
      // Add company address
      $company_location_data = array (
        'company_id' => $company_insert_id,
        'street' => Input::get('street'),
        'city' => Input::get('city'),
        'country' => Input::get('country'),
        'state' => Input::get('state'),
        'zipcode' => Input::get('zipcode'),
      );
      $company_location = new Companylocation;
      $company_location -> create($company_location_data);
      
      // Add company phones
      $company_phone = new Companyphone;
      if(Input::get('main') != "") {
        $company_main_data = array (
          'company_id' => $company_insert_id,
          'type' => 'main',
          'number' => Input::get('main')
        );
        $company_phone -> create($company_main_data);
      }
      if(Input::get('fax') != "") {
        $company_fax_data = array (
          'company_id' => $company_insert_id,
          'type' => 'fax',  
          'number' => Input::get('fax')
        );
        $company_phone -> create($company_fax_data);
      }
      
      // Add company website
      $company_website = new Companywebsite;
      if(Input::get('website') != "") {
        $company_website_data = array (
          'company_id' => $company_insert_id,
          'url' => Input::get('website')
        );
        $company_website->create($company_website_data);
      }
      
      $company_avatar = new Companyavatar;
      $company_avatar_data = array (
        'company_id' => $company_insert_id,
        'file_id_logo' => '',
        'file_id_lg' => '',
        'file_id_sm' => ''
      );
      $company_avatar->create($company_avatar_data);
            
      // Add user to company
      $company_join_data = array (
        'company_id' => $company_insert_id,
        'company_join_type' => 'admin',
        'company_join_status' => 'active'
      );
      Auth::user() -> update($company_join_data);

      Session::put('company_id', Auth::user()->company['id']);
      Session::put('company_name', Auth::user()->company['name']);
      Session::put('company_join_type', 'admin');
    
      return Redirect::to('settings/account')
              ->with('accountUpdateSuccess', '<strong>Success!</strong> Your company has been added!');
    }
  }
  
  public function updateCompany() {
    // validate the info, create rules for the inputs
    $rules = array(
      'name' => 'required',
      'type' => 'required',
      'labor' => 'required',
      'street' => 'required',
      'city' => 'required',
      'state' => 'required',
      'country' => 'required',
      'zipcode' => 'required'
    );
    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);
    
    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('settings/company/about')
        ->withErrors($validator); // send back all errors to the login form
    } else {
      
      // Get post form data
      $company_data = array (
        'name' => Input::get('name'),
        'type' => Input::get('type'),
        'labor' => Input::get('labor')
      );
      Auth::user() -> company() -> update($company_data);
      Session::put('company_name', Auth::user()->company['name']);
      
      // Update company address
      $company_location_data = array (
        'street' => Input::get('street'),
        'city' => Input::get('city'),
        'country' => Input::get('country'),
        'state' => Input::get('state'),
        'zipcode' => Input::get('zipcode'),
      );
      Companylocation::where([ 'company_id' => Auth::user()->company['id'] ])
              ->update($company_location_data);
      
      // Update company phones
      if(Input::get('main') != "") {
        $company_main_data = array (
          'number' => Input::get('main')
        );
        Companyphone::where(['company_id' => Auth::user()->company['id']])
          ->where(['type' => 'main'])
          ->update($company_main_data);
      }
      
      if(Input::get('fax') != "") {
        $company_fax_data = array (
          'number' => Input::get('fax')
        );
        Companyphone::where(['company_id' => Auth::user()->company['id']])
          ->where(['type' => 'fax'])
          ->update($company_fax_data);
      }
      
      // Update company website
      if(Input::get('website') != "") {
        $company_website_data = array (
          'url' => Input::get('website')
        );
        Companywebsite::where(['company_id' => Auth::user()->company['id']])
                ->update($company_website_data);
      }
      
      return Redirect::to('settings/company')
              ->with('UpdateSuccess', '<strong>Success!</strong> Your company information has been updated!');
    }
  }
  
  public function joinCompany() {
    
    $company_id = Input::get('company_id');
    
    // Check for a company admin
    $result = DB::table('users')
              ->where('company_id', '=', $company_id)
              ->where('company_join_type', '=', 'admin')
              ->where('company_join_status', '=', 'active')
              ->count();
    if($result > 0) {
      $company_join_type = 'standard';
    } else {
      //First active user to join company
      $company_join_type = 'admin';
    }
      
    // Get company post id
    $company = array (
      'company_id' => $company_id,
      'company_join_status' => 'active',
      'company_join_type' => $company_join_type
    );
    Auth::user() -> update($company);
    
    Session::put('company_id', Auth::user()->company['id']);
    Session::put('company_name', Auth::user()->company['name']);
    Session::put('company_join_type', $company_join_type);
            
    return Redirect::to('/settings/account')
            ->with('accountUpdateSuccess', 'You have successfully joined ' . Auth::user()->company['name'] . '.');
  }
  
  public function leaveCompany() {
    
    // validate the info, create rules for the inputs
    $rules = array(
      'leave' => 'required|in:LEAVE'
    );
    
    // custom message
    $messages = array(
      'in' => 'You must correctly type LEAVE to proceed.',
    );
    
    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules, $messages);
    
    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('settings/account')
        ->withErrors($validator); // send back all errors to the login form
    } else {
      
      $company_name = Auth::user()->company['name'];
      
      // Get company post id
      $company = array (
        'company_join_status' => 'disabled'
      );
      Auth::user() -> update($company);

      Session::forget('company_id');
      Session::forget('company_name');
      Session::forget('company_join_type');

      return Redirect::to('/settings/account')
              ->with('accountUpdateSuccess', 'You have successfully left ' . $company_name . '.');      
    }
  }
  
  public function deleteAccount() {
    
    // validate the info, create rules for the inputs
    $rules = array(
      'delete' => 'required|in:DELETE'
    );
 
    // custom message
    $messages = array(
      'in' => 'You must correctly type DELETE to proceed.',
    );

    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules, $messages);

    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('settings/account')
        ->withErrors($validator); // send back all errors to the login form
    } else {
            
      Auth::user()->update(['status' => 'disabled']);
      
      //Logout and leave
      Auth::logout();
      return Redirect::to('/login')
              ->with('accountDeleted', 'Your account has been deleted.  We\'re sorry to see you go!');
    }
  }
  
  public function showNotifications() {
    return view('settings.notifications');
  }
  
  public function showCompanyAbout() {
    $companydata = DB::table('companys')
              ->leftjoin('companylocations', 'companys.id', '=', 'companylocations.company_id')
              ->leftjoin('companywebsites', 'companys.id', '=', 'companywebsites.company_id')
              ->where('companys.id', '=', Auth::user()->company['id'])
              ->first();
    $companyphone = DB::table('companys')
              ->leftjoin('companyphones', 'companys.id', '=', 'companyphones.company_id')
              ->where('companys.id', '=', Auth::user()->company['id'])
              ->where('companyphones.type', '=', 'main')
              ->first();
    $companyfax = DB::table('companys')
              ->leftjoin('companyphones', 'companys.id', '=', 'companyphones.company_id')
              ->where('companys.id', '=', Auth::user()->company['id'])
              ->where('companyphones.type', '=', 'fax')
              ->first();
    
    $companyavatar = new Companyavatar;
    return view('settings.company.about')
            ->with(array(
              'companydata' => $companydata,
              'companyphone' => $companyphone,
              'companyfax' => $companyfax,
              'logo_url' => $companyavatar->getCompanyLogo(Auth::user()->company['id']),
              'avatar_url' => $companyavatar->getCompanyAvatar(Auth::user()->company['id'])
            ));
  }
  

  public function showCompanyPreferences() {
    
    $codes = DB::table('companycostcodes')
              ->orderby('code', 'asc')
              ->get(array('id', 'code', 'description'));
    
    return view('settings.company.preferences')->with(array(
              'codes' => $codes
            ));
  }
  
  public function showCompanyUsers() {
    $userlist = DB::table('users')
              ->where('users.company_id', '=', Session::get('company_id'))
              ->where('users.company_join_status', '=', 'active')
              ->orderby('name', 'asc')
              ->get(array('id', 'name', 'username', 'identifier', 'title', 'username', 'company_join_type'));
    
    return view('settings.company.users')
            ->with(array(
              'userlist' => $userlist
            ));
  }
  
  public function showCompanyUserRemoveModal($identifier) {
    // Return to the modal view
    $userdata = DB::table('users')
              ->where('users.identifier', '=', $identifier)
              ->first(array('identifier', 'name'));
    
    return view('settings.modals.remove_user')
            ->with('userdata', $userdata);
  }
  
  public function doCompanyUserRemoveModal($identifier) {
    // validate the info, create rules for the inputs
    $rules = array(
      'remove' => 'required|in:REMOVE'
    );
 
    // custom message
    $messages = array(
      'in' => 'You must correctly type REMOVE to proceed.',
    );

    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules, $messages);
    
    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('settings/company/users')
              ->with('UserError', 'You must correctly type REMOVE. The user was not removed.');
    } else {
      
      // Get user info
      $userdata = DB::table('users')
              ->where('users.identifier', '=', $identifier)
              ->first(array('name'));
      
      // Update company address
      $joindata = array (
        'company_join_type' => 'standard',
        'company_join_status' => 'disabled'
      );
      User::where(['identifier' => $identifier])->update($joindata);
            
      return Redirect::to('settings/company/users')
              ->with('UpdateSuccess', '<strong>Success!</strong> ' . $userdata->name . ' was removed from your company!');
    }
  }
  
  public function doCompanyUserMakeAdmin($identifier) {
    
    //Check if current user has admin authority
    if(Session::get('company_join_type') == 'admin') {
      
      // Get user info
      $userdata = DB::table('users')
              ->where('users.identifier', '=', $identifier)
              ->first(array('name'));
      
      // Update company address
      $joindata = array (
        'company_join_type' => 'admin'
      );
      User::where(['identifier' => $identifier])->update($joindata);
            
      return Redirect::to('settings/company/users')
              ->with('UpdateSuccess', '<strong>Success!</strong> ' . $userdata->name . ' is now an administrator for ' . Session::get('company_name') . '!');
    } else {
      return Redirect::to('settings/company/users')
              ->with('UserError', 'You do not have perrmission to make this user an administrator.');
    }
  }
  
}
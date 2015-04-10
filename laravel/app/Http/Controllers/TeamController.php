<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;
  use Auth;  
  use DB;
  use Response;
  
class TeamController extends Controller {

  public function listTeam() {
    
    $members = DB::table('projectusers')
                ->leftjoin('users', 'projectusers.user_id', '=', 'users.id')
                ->leftjoin('companys', 'users.company_id', '=', 'companys.id')
                ->leftjoin('userphones', 'users.id', '=', 'userphones.user_id')
                ->where('projectusers.project_id', '=', '' . Session::get('project_id') . '')
                ->where('projectusers.status', '!=', 'disabled')
                ->where('users.status', '!=', 'disabled')
                ->orderby('users.name', 'asc')
                ->get(array(
                    'users.id',
                    'users.identifier',
                    'users.name',
                    'users.title',
                    'users.email',
                    'users.company_id',
                    'users.company_join_status',
                    'userphones.mobile',
                    'projectusers.access AS projectuser_access',
                    'projectusers.status AS projectuser_status',
                    'companys.name AS company_name'
                ));
    
    foreach($members AS $member) {
      if($member->company_id == null || $member->company_join_status != 'active') {
        $member->company_name = '';
      } else {
        if($member->title != "") {
          $member->company_name = ' | ' . $member->company_name;
        } else {
          $member->company_name = $member->company_name;
        }
      }
    }
    
    return view('team.list')
            ->with(array(
                'members' => $members
            ));
  }

  public function addModal() {
    return view('team.modal.add');
  }

  public function helpModal() {
    return view('team.modal.help');
  }

  public function addTeam() {
    //Get users
    $identifier = Input::get('user');
    
    foreach($identifier as $key => $i) {
      // Get user's id
      $user = DB::table('users')
          ->where('users.identifier', '=', $identifier[$key])
          ->first(array('users.id','users.status'));
      
      $member_check = DB::table('projectusers')
          ->where('projectusers.project_id', '=', Session::get('project_id'))
          ->where('projectusers.user_id', '=', $user->id)
          ->first();
      
      if(count($member_check) > 0) {
        // Update
        Projectuser::where('user_id', '=', $user->id)
                ->where('project_id', '=', Session::get('project_id'))
                ->update(['status'=>'active']);
      } else {
        
        if($user->status == 'active') {
          $status = 'active';
        } else {
          $status = 'invited';
        }
        
        // Create
        $member_data = array (
          'project_id' => Session::get('project_id'),
          'user_id' => $user->id,
          'access' => 'write',
          'status' => $status
        );
        Projectuser::create($member_data);
      }
    }
    return Redirect::to('team');
  }
  
  public function invite() {
     // validate
    $rules = array(
      'name' => 'required',
      'email' => 'required|email|unique:users,email'
    );
    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);

    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Response::json(array(
        'fail' => true,
        'errors' => $validator->getMessageBag()->toArray()
      ));
    } else {
      // Project
      $user_data = array (
        'identifier' => Controller::RandomString('10'),
        'name' => Input::get('name'),
        'email' => Input::get('email'),
        'status' => 'static'
      );
      $newUser = User::create($user_data);
      
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
      
      $useravatar = new Useravatar;              
      $data[] = array(
        'value'=>$newUser->id,
        'identifier'=>$newUser->identifier,
        'label'=>$newUser->name,
        'title'=>'',
        'avatar'=>'<img class="avatar_company" src="' . $useravatar->getUserAvatar($newUser->id, 'sm') . '"/>'
      );
      // Return array data
      return Response::json($data);
    }
  }
  
  public function makeAdmin() {
    $identifier = Input::get('identifier');
    // Get user's id
    $user = DB::table('users')
          ->where('users.identifier', '=', $identifier)
          ->first(array('users.id'));
    
    Projectuser::where('user_id', '=', $user->id)
            ->where('project_id', '=', Session::get('project_id'))
            ->update(['access'=>'admin']);
  }
  
  public function removeMember() {
    $identifier = Input::get('identifier');
    // Get user's id
    $user = DB::table('users')
          ->where('users.identifier', '=', $identifier)
          ->first(array('users.id'));
    
    Projectuser::where('user_id', '=', $user->id)
            ->where('project_id', '=', Session::get('project_id'))
            ->update(['access'=>'standard', 'status'=>'disabled']);
  }
  
  public function distributionModal() {
    
    $lists = DB::table('distributionlists')
          ->where('distributionlists.project_id', '=', Session::get('project_id'))
          ->where('distributionlists.list_status', '=', 'active')
          ->get();
    
    return view('team.modal.distribution')
            ->with(array(
                'lists' => $lists
            ));
  }
  
  public function addDistributionList() {
    
    // Get project id
    $projectcode = Input::get('projectcode');
    $project = DB::table('projects')
          ->where('projects.code', '=', $projectcode)
          ->first(array('id'));
    
    if(count($project) > 0) {
      // Insert list
      $list = array (
        'code' => Controller::RandomString('10'),
        'project_id' => $project->id,
        'list_name' => Input::get('listname')
      );
      $listnew = Distributionlist::create($list);
      
      return $listnew->code;
    }
  }
  
  public function removeDistributionList() {
    
    // Get project id
    $code = Input::get('code');
    Distributionlist::where('code', '=', $code)
            ->where('project_id', '=', Session::get('project_id'))
            ->update(['list_status'=>'disabled']);
  }
  
  public function showDistributionList($listcode) {
    
    $listinfo = DB::table('distributionlists')
              ->where('distributionlists.code', '=', $listcode)
              ->first(array('distributionlists.list_name', 'distributionlists.code'));
    
    $members = DB::table('projectusers')
                ->leftjoin('users', 'projectusers.user_id', '=', 'users.id')
                ->leftjoin('companys', 'users.company_id', '=', 'companys.id')
                ->where('projectusers.project_id', '=', '' . Session::get('project_id') . '')
                ->where('projectusers.status', '!=', 'disabled')
                ->where('users.status', '!=', 'disabled')
                ->orderby('users.name', 'asc')
                ->get(array(
                    'users.id',
                    'users.identifier',
                    'users.name',
                    'users.company_id',
                    'users.company_join_status',
                    'projectusers.access AS projectuser_access',
                    'projectusers.status AS projectuser_status',
                    'companys.name AS company_name'
                ));
                  
    foreach($members AS $member) {
      if($member->company_id == null || $member->company_join_status != 'active') {
        $member->company_name = '';
      }
      
      $memberstatus = DB::table('distributionlistusers')
                  ->leftjoin('distributionlists', 'distributionlists.id', '=', 'distributionlistusers.list_id')
                  ->where('distributionlistusers.user_id', '=', $member->id)
                  ->where('distributionlists.code', '=', $listcode)
                  ->first(array(
                      'distributionlistusers.status AS user_list_status'
                    ));
    
      if(count($memberstatus) > 0) {            
        if($memberstatus->user_list_status != 'active') {
          $member->onswitch = "btn-default";
          $member->offswitch = "btn-danger";
        } else {
          $member->onswitch = "btn-success";
          $member->offswitch = "btn-default";
        }
      } else {
          $member->onswitch = "btn-default";
          $member->offswitch = "btn-danger";
      }
    }
    
    return view('team.distribution.users')
            ->with(array(
              'listinfo' => $listinfo,
              'members' => $members,
              'Controller' => new Controller
            ));
    
  }
  
  public function toggleDistribution() {
    // Get post data
    $status = Input::get('status');
    $listcode = Input::get('listcode');
    $identifier = Input::get('identifier');
        
    // Get user's id
    $user = DB::table('users')
        ->where('users.identifier', '=', $identifier)
        ->first(array('users.id'));
        
    // Get user's id
    $list = DB::table('distributionlists')
        ->where('distributionlists.code', '=', $listcode)
        ->first(array('distributionlists.id'));

    $member_dist_check = DB::table('distributionlistusers')
        ->where('list_id', '=', $list->id)
        ->where('user_id', '=', $user->id)
        ->first();

    if(count($member_dist_check) > 0) {
      
      // Check status
      if($status == 'active') {
        $status = 'active';
      } else {
        $status = 'disabled';
      }
      
      // Update
      Distributionlistuser::where('user_id', '=', $user->id)
              ->where('list_id', '=', $list->id)
              ->update(['status'=>$status]);
    } else {

      // Create
      $data = array (
        'list_id' => $list->id,
        'user_id' => $user->id,
        'status' => 'active'
      );
      Distributionlistuser::create($data);
    }
  }  
  
}
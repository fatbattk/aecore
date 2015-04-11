<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;
  use Auth;
  use DB;
  use Response;
  
  use App\Models\User;
  use App\Models\Useravatar;
  use App\Models\Companyavatar;
  
  class AutocompleteController extends Controller {
    
    public function Companies() {
      
      // Get search term
      $term = Input::get('term');
      
      // Run query
      $result = DB::table('companys')->orderBy('companys.name', 'asc')
                ->leftjoin('companylocations', 'companys.id', '=', 'companylocations.company_id')
                ->where('companys.name', 'LIKE', '%' . $term . '%')
                ->get(array(
                    'companys.id',
                    'companys.name',
                    'companylocations.city',
                    'companylocations.state'
                  ));
      
      // Build array
      $data = array();
      $companyavatar = new Companyavatar;    
      
      foreach($result as $row){
        
        if ($row->city != "" && $row->state != "") {
          $location = $row->city . ', ' . $row->state;
        } elseif ($row->city != "" && $row->state == "") {
          $location = $row->city;
        } else {
          $location = '';
        }
        
        $data[] = array(
          'value'=>$row->id,
          'label'=>$row->name,
          'logo'=>'<img class="avatar_company" src="' . $companyavatar->getCompanyAvatar($row->id) . '"/>',
          'location'=>'<span class="small text-muted">' . $location . '</span>'
        );
      }
      // Return array data
      return Response::json($data);
    }
    
    public function Users() {
      
      // Get search term
      $term = Input::get('term');
      
      // Run query
      $result = DB::table('users')
                ->where('users.name', 'LIKE', '%' . $term . '%')
                ->where('users.status', '=', 'active')
                ->orderBy('users.name', 'asc')
                ->get(array(
                    'users.id',
                    'users.identifier',
                    'users.name',
                    'users.title'
                  ));
      
      // Build array
      $data = array();
      $useravatar = new Useravatar;
      
      foreach($result as $row){        
        $data[] = array(
          'value'=>$row->id,
          'identifier'=>$row->identifier,
          'label'=>$row->name,
          'title'=>$row->title,
          'avatar'=>'<img class="avatar_company" src="' . $useravatar->getUserAvatar($row->id, 'sm') . '"/>'
        );
      }
      // Return array data
      return Response::json($data);
    }
    
    public function Tasklists() {
      
      // Get search term
      $term = Input::get('term');
      
      // Run query
      $result = DB::table('tasklists')
                ->where('tasklists.list', 'LIKE', '%' . $term . '%')
                ->where('tasklists.status', '=', 'active')
                ->where('tasklists.user_id', '=', Auth::User()->id)
                ->orderBy('tasklists.list', 'asc')
                ->get(array(
                    'tasklists.listcode',
                    'tasklists.list'
                  ));
      
      // Build array
      $data = array();
      $useravatar = new Useravatar;
      
      foreach($result as $row){        
        $data[] = array(
          'value'=>$row->listcode,
          'label'=>$row->list
        );
      }
      // Return array data
      return Response::json($data);
    }
    
    public function Plansheets() {
      
      // Get search term
      $term = Input::get('term');
      
      // Run query
      $result = DB::table('plansetsheets AS t1')
                ->leftjoin('plansets', 'plansets.id', '=', 't1.planset_id')
                ->leftjoin('plansetsheets AS t2', function($query) {
                  $query->on('t1.sheet_number','=','t2.sheet_number');
                  $query->on('t1.sheet_revision','<','t2.sheet_revision');
                })
                ->where('plansets.project_id', '=', Session::get('project_id'))
                ->where('t1.sheet_status', '=', 'processed')
                ->where('t1.sheet_number', 'LIKE', '%%' . $term . '%%')
                  ->orwhere('t1.sheet_name', 'LIKE', '%%' . $term . '%%')
                ->where('t2.id', null)
                ->orderby('t1.sheet_discipline', 'asc')
                ->orderby(DB::raw('LENGTH(t1.sheet_number) asc, t1.sheet_number'), 'asc')
                ->get(array('t1.sheet_code', 't1.sheet_number', 't1.sheet_name'));
      
      // Build array
      $data = array();      
      foreach($result as $row){        
        $data[] = array(
          'value'=>$row->sheet_code,
          'label'=>$row->sheet_number . ' ' . $row->sheet_name
        );
      }
      // Return array data
      return Response::json($data);
    }    
    
  }
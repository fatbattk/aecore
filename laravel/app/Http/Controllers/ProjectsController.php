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

  class ProjectsController extends Controller {

    public function listProjects() {
      if(Session::has('company_id')) {
        $projects = DB::table('projects')
                ->select([
                  'projects.code',
                  'projects.name',
                  'projects.status',
                  'projects.size',
                  'projects.size_unit',
                  'projectvalues.value',
                  DB::raw('primaryDate.date_archive AS primaryArchive'),
                  DB::raw('primaryDate.date_start AS primaryStart'),
                  DB::raw('primaryDate.date_finish AS primaryFinish'),
                  DB::raw('altDate.date_start AS altStart'),
                  DB::raw('altDate.date_finish AS altFinish'),
                  DB::raw('primaryNumber.number AS primaryNumber'),
                  DB::raw('altNumber.number AS altNumber')])
                ->leftjoin('projectnumbers AS primaryNumber', 'projects.id', '=', 'primaryNumber.project_id')
                ->leftjoin('projectnumbers AS altNumber', 'projects.id', '=', 'altNumber.project_id')
                ->leftjoin('projectdates AS primaryDate', 'projects.id', '=', 'primaryDate.project_id')
                ->leftJoin('projectdates AS altDate', function($join){
                  $join->on('projects.id', '=', 'altDate.project_id');
                  $join->on('altDate.company_id', '=', DB::raw('' . Auth::User()->company['id'] . ''));
                })
                ->leftJoin('projectvalues', function($join){
                  $join->on('projects.id', '=', 'projectvalues.project_id');
                  $join->on('projectvalues.company_id', '=', DB::raw('' . Auth::User()->company['id'] . ''));
                })
                ->leftjoin('projectlocations', 'projects.id', '=', 'projectlocations.project_id')
                ->leftjoin('projectusers', 'projects.id', '=', 'projectusers.project_id')
                ->where('projectusers.user_id', '=', '' . Auth::user()->id . '')
                ->where('projects.status', '!=', 'Archived')
                ->where('projects.status', '!=', 'Completed')
                ->orderby('altNumber.number', 'asc')
                ->orderby('primaryNumber.number', 'asc')
                ->get();
                
        foreach($projects AS $project) {
          
          //Format number
          if($project->altNumber != null && $project->altNumber != "") {
            $project->number = $project->altNumber;
          } elseif(isset($project->primaryNumber) && $project->primaryNumber != "") {
            $project->number = $project->primaryNumber;
          }

          //Format date start
          if($project->altStart != null && $project->altStart != "") {
            $project->start = date('m/d/Y', strtotime($project->altStart));
          } elseif(isset($project->primaryStart) && $project->primaryStart != "") {
            $project->start = date('m/d/Y', strtotime($project->primaryStart));
          } else {
            $project->start = "";
          }

          //Format date finish
          if(isset($project->altFinish) && $project->altFinish != "") {
            $project->finish = date('m/d/Y', strtotime($project->altFinish));
          } elseif(isset($project->primaryFinish) && $project->primaryFinish != "") {
            $project->finish = date('m/d/Y', strtotime($project->primaryFinish));
          } else {
            $project->finish = "";
          }
        
          // Format blank size
          if($project->size == '') {
            $project->size = 'N/A';
          }
          
          // Format size unit
          if($project->size_unit == 'feet' && $project->size != '') {
            $project->size_unit = 'SF';
          } elseif($project->size_unit == 'meters' && $project->size != '') {
            $project->size_unit = 'SM';
          }
        }
        
        return View::make('projects.list')
                ->with(array(
                  'projects' => $projects
                ));
      } else {
        return Redirect::to('welcome/company');
      }
    }

    public function listProjectsDropdown() {
      if(Session::has('company_id')) {
        $projects = DB::table('projects')
                ->select([
                  'projects.code',
                  'projects.name',
                  DB::raw('primaryNumber.number AS primaryNumber'),
                  DB::raw('altNumber.number AS altNumber')])
                ->leftjoin('projectnumbers AS primaryNumber', 'projects.id', '=', 'primaryNumber.project_id')
                ->leftjoin('projectnumbers AS altNumber', 'projects.id', '=', 'altNumber.project_id')
                ->leftjoin('projectusers', 'projects.id', '=', 'projectusers.project_id')
                ->where('projectusers.user_id', '=', '' . Auth::user()->id . '')
                ->where('projects.status', '!=', 'Archived')
                ->where('projects.status', '!=', 'Completed')
                ->orderby('altNumber.number', 'asc')
                ->orderby('primaryNumber.number', 'asc')
                ->get();
        
        echo '<select class="form-control sidebar-project-list" onChange="location.href=\'/projects/launch/\'+this.options[this.selectedIndex].value;">';
        foreach($projects AS $project) {
          //Format number
          if($project->altNumber != null && $project->altNumber != "") {
            $project->number = $project->altNumber;
          } elseif(isset($project->primaryNumber) && $project->primaryNumber != "") {
            $project->number = $project->primaryNumber;
          }
          
          if(Session::get('project_code') == $project->code) {
            $selected = 'selected="selected"';
          } else {
            $selected = '';
          }
          echo '<option value="'.$project->code.'" '.$selected.'>#' . $project->number . ' '. $project->name . '</option>';
        }
        echo '</select>';
      }
    }
    
    public function newProject() {
      return View::make('projects.new');
    }

    public function newProjectSave() {
      // validate the info, create rules for the inputs
      $rules = array(
        'status' => 'required',
        'type' => 'required',
        'number' => 'required',
        'name' => 'required',
        'start' => 'required',
        'finish' => 'required',
        'submittal_code' => 'required'
      );

      // run the validation rules on the inputs from the form
      $validator = Validator::make(Input::all(), $rules);

      // if the validator fails, redirect back to the form
      if ($validator->fails()) {
        return Redirect::to('projects/new')
          ->withErrors($validator); // send back all errors to the login form
      } else {
        
        // Project
        $project_data = array (
          'company_id' => Auth::user()->company['id'],
          'code' => Controller::RandomString('10'),
          'status' => Input::get('status'),
          'type' => Input::get('type'),
          'name' => Input::get('name'),
          'size' => Input::get('size'),
          'size_unit' => Input::get('size_unit')
        );
        $project = Project::create($project_data);
        
        // Project number
        $project_number_data = array (
          'project_id' => $project->id,
          'company_id' => Auth::user()->company['id'],
          'number' => Input::get('number')
        );
        Projectnumber::create($project_number_data);
        
        // Project dates
        $start = new DateTime(Input::get('start'));
        $start = $start->format('Y-m-d H:i:s');
        $finish = new DateTime(Input::get('finish'));
        $finish = $finish->format('Y-m-d H:i:s');
      
        $project_date_data = array (
          'project_id' => $project->id,
          'company_id' => Auth::user()->company['id'],
          'date_start' => $start,
          'date_finish' => $finish,
        );
        Projectdate::create($project_date_data);
        
        // Project value
        $project_value_data = array (
          'project_id' => $project->id,
          'company_id' => Auth::user()->company['id'],
          'value' => Input::get('value')
        );
        Projectvalue::create($project_value_data);
        
        // Project description
        $project_description_data = array (
          'project_id' => $project->id,
          'description' => Input::get('description')
        );
        Projectdescription::create($project_description_data);
        
        // Project location
        $project_location_data = array (
          'project_id' => $project->id,
          'street' => Input::get('street'),
          'city' => Input::get('city'),
          'country' => Input::get('country'),
          'state' => Input::get('state'),
          'zipcode' => Input::get('zipcode'),
        );
        Projectlocation::create($project_location_data);
        
        // Add user as admin
        $member_data = array (
          'project_id' => $project->id,
          'user_id' => Auth::User()->id,
          'access' => 'admin',
          'status' => 'active'
        );
        Projectuser::create($member_data);
        
        return Redirect::to('/projects')
                ->with('success', '<strong>Success!</strong> Your project has been created.');
      }
    }

    public function editProject($code) {
      $check = DB::table('projects')
                  ->where('projects.code', '=', $code)
                  ->first(array('id'));
      if(count($check) == 0) {
        return Redirect::to('projects');
      } else {
        
        // Get project data
        $project = DB::table('projects')
                ->select(['*',
                  DB::raw('primaryDate.date_archive AS primaryArchive'),
                  DB::raw('primaryDate.date_start AS primaryStart'),
                  DB::raw('primaryDate.date_finish AS primaryFinish'),
                  DB::raw('altDate.date_start AS altStart'),
                  DB::raw('altDate.date_finish AS altFinish'),
                  DB::raw('primaryNumber.number AS primaryNumber'),
                  DB::raw('altNumber.number AS altNumber')])

                ->leftjoin('projectnumbers AS primaryNumber', 'projects.id', '=', 'primaryNumber.project_id')
                ->leftjoin('projectnumbers AS altNumber', 'projects.id', '=', 'altNumber.project_id')

                ->leftjoin('projectdates AS primaryDate', 'projects.id', '=', 'primaryDate.project_id')
                ->leftJoin('projectdates AS altDate', function($join){
                  $join->on('projects.id', '=', 'altDate.project_id');
                  $join->on('altDate.company_id', '=', DB::raw('' . Auth::User()->company['id'] . ''));
                })
                ->leftJoin('projectvalues', function($join){
                  $join->on('projects.id', '=', 'projectvalues.project_id');
                  $join->on('projectvalues.company_id', '=', DB::raw('' . Auth::User()->company['id'] . ''));
                })
                ->leftjoin('projectdescriptions', 'projects.id', '=', 'projectdescriptions.project_id')
                ->leftjoin('projectlocations', 'projects.id', '=', 'projectlocations.project_id')
                ->where('projects.code', '=', '' . $code . '')
                ->where('projects.company_id', '=', '' . Auth::user()->company['id'] . '')
                ->first();

        if($project->company_id == Auth::User()->company['id'] ) {
          $disabled = '';
        } else {
          $disabled = 'disabled';
        }

        //Format number
        if($project->altNumber != null && $project->altNumber != "") {
          $project->number = $project->altNumber;
        } elseif(isset($project->primaryNumber) && $project->primaryNumber != "") {
          $project->number = $project->primaryNumber;
        }

        //Format date archived
        $project->archive = date('mdY', strtotime($project->primaryArchive));
        if(isset($project->archive) && $project->archive != "01011970") {
          $project->archive = date('m/d/Y', strtotime($project->primaryArchive));
        } else {
          $project->archive = "";
        }
        
        //Format date start
        if($project->altStart != null && $project->altStart != "") {
          $project->start = date('m/d/Y', strtotime($project->altStart));
        } elseif(isset($project->primaryStart) && $project->primaryStart != "") {
          $project->start = date('m/d/Y', strtotime($project->primaryStart));
        } else {
          $project->start = "";
        }

        //Format date finish
        if(isset($project->altFinish) && $project->altFinish != "") {
          $project->finish = date('m/d/Y', strtotime($project->altFinish));
        } elseif(isset($project->primaryFinish) && $project->primaryFinish != "") {
          $project->finish = date('m/d/Y', strtotime($project->primaryFinish));
        } else {
          $project->finish = "";
        }
        
        // Format size units
        if($project->size_unit == 'feet') {
          $project->size_unit_text = 'SQ FT';
        } else {
          $project->size_unit_text = 'SQ M';
        }
                
        return View::make('projects.edit')
                ->with(array(
                  'project' => $project,
                  'disabled' => $disabled
                ));
      }
    }
    
    public function editProjectSave($code) {
      
      // validate the info, create rules for the inputs
      $rules = array(
        'status' => 'required',
        'type' => 'required',
        'number' => 'required',
        'name' => 'required',
        'start' => 'required',
        'finish' => 'required',
        'submittal_code' => 'required'
      );

      // run the validation rules on the inputs from the form
      $validator = Validator::make(Input::all(), $rules);

      // if the validator fails, redirect back to the form
      if ($validator->fails()) {
        return Redirect::to('projects/new')
          ->withErrors($validator); // send back all errors to the login form
      } else {
        
        // Get project id
        $project = DB::table('projects')
                  ->where('projects.code', '=', $code)
                  ->first(array('id'));
        
        if(count($project) > 0) {
          // Project
          $project_data = array (
            'status' => Input::get('status'),
            'type' => Input::get('type'),
            'name' => Input::get('name'),
            'size' => Input::get('size'),
            'size_unit' => Input::get('size_unit')
          );
          Project::where('id', '=', $project->id)
                  ->update($project_data);

          // Project number
          $number = DB::table('projectnumbers')
                    ->where('project_id', '=', $project->id)
                    ->where('company_id', '=', Auth::user()->company['id'])
                    ->first(array('id'));
          
          if(count($number) > 0) {
            // Update
            Projectnumber::where('project_id', '=', $project->id)
                  ->where('company_id', '=', Auth::user()->company['id'])
                  ->update(['number'=>Input::get('number')]);
          } else {
            // Insert new
            $project_number_data = array (
              'project_id' => $project->id,
              'company_id' => Auth::user()->company['id'],
              'number' => Input::get('number')
            );
            Projectnumber::create($project_number_data);
          }

          // Project dates
          $start = new DateTime(Input::get('start'));
          $start = $start->format('Y-m-d H:i:s');
          $finish = new DateTime(Input::get('finish'));
          $finish = $finish->format('Y-m-d H:i:s');
          $archive = new DateTime(Input::get('archive'));
          $archive = $archive->format('Y-m-d H:i:s');
          
          $date = DB::table('projectdates')
                    ->where('project_id', '=', $project->id)
                    ->where('company_id', '=', Auth::user()->company['id'])
                    ->first(array('id'));
          
          if(count($date) > 0) {
            // Update
            Projectdate::where('project_id', '=', $project->id)
                  ->where('company_id', '=', Auth::user()->company['id'])
                  ->update(['date_start'=>$start,'date_finish'=>$finish,'date_archive'=>$archive]);
          } else {
            // Insert new
            $project_date_data = array (
              'project_id' => $project->id,
              'company_id' => Auth::user()->company['id'],
              'date_start' => $start,
              'date_finish' => $finish,
              'date_archive' => $archive
            );
            Projectdate::create($project_date_data);
          }          

          // Project value
          $value = DB::table('projectvalues')
                    ->where('project_id', '=', $project->id)
                    ->where('company_id', '=', Auth::user()->company['id'])
                    ->first(array('id'));
          
          if(count($value) > 0) {
            // Update
            Projectvalue::where('project_id', '=', $project->id)
                  ->where('company_id', '=', Auth::user()->company['id'])
                  ->update(['value'=>Input::get('value')]);
          } else {
            // Insert new
            $project_value_data = array (
              'project_id' => $project->id,
              'company_id' => Auth::user()->company['id'],
              'value' => Input::get('value')
            );
            Projectvalue::create($project_value_data);
          }

          // Project description
          Projectdescription::where('project_id', '=', $project->id)
                  ->update(['description'=>Input::get('description')]);

          // Project location
          $project_location_data = array (
            'street' => Input::get('street'),
            'city' => Input::get('city'),
            'country' => Input::get('country'),
            'state' => Input::get('state'),
            'zipcode' => Input::get('zipcode'),
          );
          Projectlocation::where('project_id', '=', $project->id)
                  ->update($project_location_data);          

          return Redirect::to('/projects')
                  ->with('success', '<strong>Success!</strong> Your project information has been updated.');
        }
      }
    }
    
    public function launchProject($code) {
      $project = DB::table('projects')
              ->select(['projects.id', 'projects.code', 'projects.name','projectusers.access',
                DB::raw('primaryNumber.number AS primaryNumber'),
                DB::raw('altNumber.number AS altNumber')])
              ->leftjoin('projectnumbers AS primaryNumber', 'projects.id', '=', 'primaryNumber.project_id')
              ->leftjoin('projectnumbers AS altNumber', 'projects.id', '=', 'altNumber.project_id')
              ->leftjoin('projectusers', 'projects.id', '=', 'projectusers.project_id')
              ->where('projects.code', '=', $code)
              ->where('projectusers.user_id', '=', Auth::USer()->id)
              ->first();
        
      //Format number
      if($project->altNumber != null && $project->altNumber != "") {
        $project->number = $project->altNumber;
      } elseif(isset($project->primaryNumber) && $project->primaryNumber != "") {
        $project->number = $project->primaryNumber;
      }
      
      if(count($project) > 0) {
        Session::put('project_id', $project->id);
        Session::put('project_code', $project->code);
        Session::put('project_number', $project->number);
        Session::put('project_name', $project->name);
        Session::put('project_access', $project->access);
        return Redirect::to('dashboard');
      } else {
        return Redirect::to('projects');
      }
    }
  }
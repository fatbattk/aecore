<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;
  use Auth;
  use DB;
  
class DashboardController extends Controller {

  public function showDashboard() {
    
    $project = "";
    
    return view('dashboard.index')
            ->with(array(
                'project' => $project
            ));
  }

}
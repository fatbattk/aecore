<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;
  use Auth;
  use DB;
  
class RfiController extends Controller {

  public function showRfiLog() {
    
    $project = "";
    
    return view('rfis.list')
            ->with(array(
                'project' => $project
            ));
  }

}
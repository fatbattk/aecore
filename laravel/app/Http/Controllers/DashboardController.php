<?php

class DashboardController extends BaseController {

  public function showDashboard() {
    
    $project = "";
    
    return view('dashboard.index')
            ->with(array(
                'project' => $project
            ));
  }

}
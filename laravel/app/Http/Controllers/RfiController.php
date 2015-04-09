<?php

class RfiController extends BaseController {

  public function showRfiLog() {
    
    $project = "";
    
    return view('rfis.list')
            ->with(array(
                'project' => $project
            ));
  }

}
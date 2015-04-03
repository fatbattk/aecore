<?php

  //Notifications
  /*
  $notify1 = new_message_count();
  $notify2 = submittal_bic_count();
  $notify3 = rfi_bic_count();
  $notify = array_merge($notify1, $notify2, $notify3);
  */
    
  //Array for navigation items
  $items = array(
            array("r" => "dashboard", "default_id" => "", "bubble"=>"0", "label" => "Dashboard", "title" => "Dashboard", "glyph" => "glyphicon-stats"),
            array("r" => "team", "default_id" => "", "bubble"=>"0", "label" => "Team", "title" => "Project Team", "glyph" => "glyphicon-user"),
            array("r" => "rfis", "default_id" => "", "bubble"=>"0", "label" => "RFIs", "title" => "Requests For Information", "glyph" => "glyphicon-question-sign"),
            array("r" => "planroom", "default_id" => "", "bubble"=>"0", "label" => "Plan Room", "title" => "Plan Room", "glyph" => "glyphicon-th-large"),
            
      //
            //array("r"=>"bidding", "default_id"=>"bidlist", "bubble"=>"0", "label"=>"Bidding", "title"=>"Bidding", "glyph" => "glyphicon-bullhorn"),
            //array("r"=>"cost", "default_id"=>"budget", "bubble"=>"0", "label"=>"Budget", "title"=>"", "glyph" => "glyphicon-usd"),
            //array("r" => "engineering", "default_id" => "documents", "bubble"=>"0", "label" => "Documents", "title" => "Documents", "glyph" => "glyphicon-folder-open"),
            
            //array("r" => "engineering", "default_id" => "submittals", "bubble"=>"0", "label" => "Submittals", "title" => "Submittals", "glyph" => "glyphicon-tags"),
            
            //array("r"=>"engineering", "default_id"=>"minutes", "bubble"=>"0", "label"=>"Meeting Minutes", "title"=>"Meeting Minutes", "glyph" => "glyphicon-pencil"),
            //array("r" => "field", "default_id" => "dcr", "bubble"=>"0", "label" => "Daily Reports", "title" => "Daily Construction Reports", "glyph" => "glyphicon-book", "collab_type" => array("Subcontractor" => TRUE, "General Contractor" => TRUE)),
            //array("r" => "field", "default_id" => "punchlist", "bubble"=>"0", "label" => "Punchlist", "title" => "Punchlist", "glyph" => "glyphicon-star")
          );

  $nav = '';
  foreach ($items as $val) {
    //Parse URL
    if($val['default_id'] != "") {
      if(isset($_GET['id']) && ($_GET['id'] == $val['default_id'])) {
        $active = Request::is($val['r']) ? ' class="active"' : '';
      } else {
        $active = '';
      }
      $val['default_id'] = "/".$val['default_id'];
    } else {
      $active = Request::is($val['r'].'*') ? ' class="active"' : '';
      $val['default_id'] = "";
    }

    //Check if notification bubbles exist
    if($val['bubble'] > 0) { 
      $bubble = ' (' . $val['bubble'] . ')';
    } else {
      $bubble = "";
    }

    $nav .= '<li><a href="/' . $val['r'] . $val['default_id'] . '" title="' . $val['title'] . '"';
    $nav .= $active;
    if (isset($val['id'])) {
      $nav .= ' id=' . $val['id'];
    }
    $nav .= '><span class="glyphicon ' . $val['glyph'] . '"></span> ' . $val['label'] . $bubble .'</a></li>';
  }
?>

<div class="sidebar-wrapper" id="projectnav">
  <ul class="sidebar-nav">
    {{ App::make('ProjectsController')->listProjectsDropdown() }}
    <span class="btn btn-primary pull-right toggle-nav" style="margin:5px 10px 0 0;padding:6px;" onClick="$('#projectnav').toggle();"><span class="glyphicon glyphicon-menu-hamburger" style="margin:0;"></span></span>
    <li class="nav-header">Project Navigation</li>
    <?php
      //Nav output
      echo $nav;
    ?>
  </ul>
</div>
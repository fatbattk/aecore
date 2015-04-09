@extends('layouts.application.main_wide')

@extends('layouts.application.project_nav')
@section('content')

<div class="page-wrapper">
  <div class="pagehead">
    <div class="container-fluid">
      <span class="btn btn-primary pull-left toggle-nav" style="margin-right:10px;padding:7px;" onClick="$('#projectnav').toggle();"><span class="glyphicon glyphicon-menu-hamburger"></span></span>
      <h1>Dashboard</h1>
    </div>
  </div>

  <div class="container-fluid">
    {!! Session::get('project_id') !!}
  </div>
</div>

@stop
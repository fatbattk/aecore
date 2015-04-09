@extends('layouts.application.main')
@section('content')

  <div class="pagehead">
    <div class="container">
      <h1>Settings / Company / Options</h1>
    </div>
  </div>
      
  <div class="container">
    <div class="row">
      @include('settings.nav')
      <div class="col-sm-8 col-md-9">
        
        @if(Session::has('UpdateSuccess'))
          <script type="text/javascript" charset="utf-8">
            setTimeout(function() {
              $("#deletesuccess").fadeOut("slow");
            }, 2500);
          </script>
          <div class="alert alert-success" id="deletesuccess"><span class="glyphicon glyphicon-ok"></span> {!! Session::get('UpdateSuccess') !!}</div>
        @endif

        <div class="panel panel-default">
          <div class="panel-heading">Cost Codes</div>
          <div class="panel-body">
          </div>
        </div>
        
      </div>
    </div>
  </div>
@stop
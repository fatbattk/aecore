@extends('layouts.application.main')
@section('content')

  <script type="text/javascript">
    $('#mobile').text(function(i, text) {
      return text.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
    });
  </script>

  <div class="pagehead">
    <div class="container">
      <h1>Settings / Notifications</h1>
    </div>
  </div>
  
  <div class="container">  
    <div class="row">  
      @include('settings.nav')
      <div class="col-sm-8 col-md-9">
        @if (Session::has('UpdateSuccess'))
          <script type="text/javascript" charset="utf-8">
            setTimeout(function() {
              $("#deletesuccess").fadeOut("slow");
            }, 3000);
          </script>
          <div class="alert alert-success" id="deletesuccess"><span class="glyphicon glyphicon-ok"></span> {!! Session::get('UpdateSuccess') !!}</div>
        @endif        
        <div class="panel panel-default">
          <div class="panel-heading">Change Password</div>
          <div class="panel-body">
            {!! Form::open(array('url' => 'settings/account/password', 'method' => 'post', 'class' => 'form-horizontal')) !!}

            <div class="form-group">
              {!! Form::label('old_password', 'Old Password', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::password('old_password', array('class' => 'form-control', 'placeholder' => 'Old Password' )) !!}
                <span class="text-danger">{!! $errors->first('old_password') !!}</span>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('new_password', 'New Password', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::password('new_password', array('class' => 'form-control', 'placeholder' => 'New Password' )) !!}
                <span class="text-danger">{!! $errors->first('new_password') !!}</span>
              </div>
            </div>

            <div class="form-group no-margin">
              <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
                {!! Form::submit('Update Profile', array('class' => 'btn btn-success')) !!}
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
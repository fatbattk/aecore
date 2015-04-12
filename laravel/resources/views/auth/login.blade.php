@extends('layouts.storefront.main')
@section('content')
  <div class="col-md-4 col-md-offset-4" style="padding-top:18%;">
    
    @if (Session::has('accountDeleted'))
      <script type="text/javascript" charset="utf-8">
        setTimeout(function() {
          $("#deleteerror").fadeOut("slow");
        }, 3000);
      </script>
      <div class="alert alert-success" id="deleteerror"><span class="glyphicon glyphicon-check"></span> {!! Session::get('accountDeleted') !!}</div>
    @endif
    
    @if (Session::has('dangerMessage'))
    <div class="alert alert-danger"><span class="glyphicon glyphicon-alert"></span> {!! Session::get('dangerMessage') !!} <br> {!! link_to('reset', 'Forgot Password?', array('class'=>'btn-link btn-spacer-left')) !!}</div>
    @endif
    @if (Session::has('warningMessage'))
      <div class="alert alert-warning"><span class="glyphicon glyphicon-alert"></span> {!! Session::get('warningMessage') !!} </div>
    @endif
    
    <h4 class="text-muted">Log In</h4>
    {!! Form::open(array('url' => 'login', 'method' => 'post', 'style'=>'margin-bottom:20px')) !!}
    
    <div class="form-group">
      <span class="text-danger">{!! $errors->first('email') !!}</span>
      {!!Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'Email', 'autofocus' => 'true'))!!}
    </div>

    <div class="form-group">
      <span class="text-danger">{!! $errors->first('password') !!}</span>
      {!!Form::password('password',array('class' => 'form-control', 'placeholder' => 'Password'))!!}
    </div>

    {!!Form::submit('Log In', array('class' => 'btn btn-success'))!!}
    <span class="btn-spacer-left">First time here? {!! link_to('signup', 'Create an Account', array('class'=>'bold')) !!}</span>
    {!! Form::close() !!}
  </div>
@stop
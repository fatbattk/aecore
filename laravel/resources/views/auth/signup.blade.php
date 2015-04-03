@extends('layouts.storefront.main')
@section('content')
  <div class="col-md-4 col-md-offset-4" style="padding-top:18%;">
    <h4 class="text-muted">Create an Account</h4>
        
    @if (Session::has('signupFailed'))
      <div class="alert alert-danger"><span class="glyphicon glyphicon-alert"></span> {!! Session::get('signupFailed') !!}</div>
    @endif
    
    {!! Form::open(array('url' => 'signup', 'method' => 'post')) !!}

    <div class="form-group">
      <span class="text-danger">{!! $errors->first('name') !!}</span>
      {!!Form::text('name', null, array('class'=>'form-control', 'placeholder'=>'Full name', 'autofocus' => 'true'))!!}
    </div>

    <div class="form-group">
      <span class="text-danger">{!! $errors->first('username') !!}</span>
      {!!Form::text('username', null, array('class'=>'form-control', 'placeholder'=>'Username'))!!}
    </div>

    <div class="form-group">
      <span class="text-danger">{!! $errors->first('email') !!}</span>
      {!!Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'Email address'))!!}
    </div>

    <div class="form-group">
      <span class="text-danger">{!! $errors->first('password') !!}</span>
      {!!Form::password('password',array('class' => 'form-control', 'placeholder' => 'Password'))!!}
    </div>

    {!!Form::submit('Sign Up', array('class' => 'btn btn-success', 'title' => 'Create a new Aecore account.'))!!}
    <span class="btn-spacer-left">Have an account? {!! link_to('login', 'Log In Here', array('class'=>'btn-link bold')) !!}</span>
    {!! Form::close() !!}
  </div>
@stop
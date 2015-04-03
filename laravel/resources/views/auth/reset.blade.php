@extends('layouts.storefront.main')
@section('content')
  <div class="col-md-4 col-md-offset-4" style="padding-top:18%;">
    <h4 class="text-muted">Reset Password</h4>
    <p class="small">Enter your email address and we'll send you instructions to reset your password.</p>
    {!! Form::open(array('url' => 'login', 'method' => 'post', 'style'=>'margin-bottom:20px')) !!}

    <div class="form-group">
      {!!Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'Email', 'autofocus' => 'true'))!!}
      <span class="text-danger">{!! $errors->first('email') !!}</span>
    </div>

    {!!Form::submit('Send', array('class' => 'btn btn-success'))!!}
    <span class="btn-spacer-left">Have an account? {!! link_to('login', 'Log In Here', array('class'=>'btn-link bold')) !!}</span>
    {!! Form::close() !!}

    <div class="alert alert-warning">First time here? {!! link_to('signup', 'Create an Account', array('class'=>'bold')) !!}</div>
  </div>
@stop
<<<<<<< HEAD
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
=======
@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Reset Password</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="token" value="{{ $token }}">

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Confirm Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password_confirmation">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Reset Password
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
>>>>>>> c30b6609d3e2c78ca3d66f166c1505caccb22195

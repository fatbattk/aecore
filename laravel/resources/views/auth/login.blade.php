<<<<<<< HEAD
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
      <div class="alert alert-danger"><span class="glyphicon glyphicon-alert"></span> {!! Session::get('dangerMessage') !!}</div>
    @endif
    @if (Session::has('warningMessage'))
      <div class="alert alert-warning"><span class="glyphicon glyphicon-alert"></span> {!! Session::get('warningMessage') !!}</div>
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
    {!! link_to('reset', 'Forgot Password?', array('class'=>'btn-link btn-spacer-left')) !!}

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
				<div class="panel-heading">Login</div>
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

					<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

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
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> Remember Me
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">Login</button>

								<a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a>
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

@extends('layouts.application.main')
@section('content')

  <div class="pagehead">
    <div class="container">
      <h1>Settings / Company / Users</h1>
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
        @if(Session::has('UserError'))
          <script type="text/javascript" charset="utf-8">
            setTimeout(function() {
              $("#deletesuccess").fadeOut("slow");
            }, 2500);
          </script>
          <div class="alert alert-danger" id="deletesuccess"><span class="glyphicon glyphicon-warning-sign"></span> {!! Session::get('UserError') !!}</div>
        @endif
        <div class="panel panel-default">
          <div class="panel-heading">{!! Session::get('company_name') !!} Users</div>
          <div class="panel-body" style="padding:0;">
            <?php $useravatar = new App\Models\Useravatar; ?>
            @foreach($userlist as $user)
              <div class="user-list col-md-6" <?php if($user->id != Auth::User()->id) { ?> onMouseOver="$('#user_settings_<?php echo $user->id; ?>').show();" onMouseOut="$('#user_settings_<?php echo $user->id; ?>').hide();" <?php } ?> >
                <div class="btn-group pull-right" id="user_settings_{!! $user->id !!}" style="margin-top:5px;display:none;">
                  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="/settings/company/users/remove/{!! $user->identifier !!}" data-target="#modal" data-toggle="modal"><span class="glyphicon glyphicon-trash"></span> Remove User</a></li>
                    @if($user->company_join_type != 'admin')
                    <li><a href="/settings/company/users/admin/{!! $user->identifier !!}"><span class="glyphicon glyphicon-tower"></span> Make Admin</a></li>
                    @endif
                  </ul>
                </div>
                <img src="{!! $useravatar->getUserAvatar($user->id, 'sm') !!}" class="avatar_md" />
                <p class="bold">{!! $user->name !!} @if($user->company_join_type == 'admin') {!! '<span class="small text-muted">(Admin)</span>' !!} @endif</p>
                <p class="small text-muted">{!! link_to('/profile/'.$user->identifier, '@'.$user->username, array('class' => 'btn-link')) !!}  {!! ' ' . $user->title !!}</p>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
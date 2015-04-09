<div class="col-sm-4 col-md-3">
  <div class="panel panel-default">
    <div class="panel-heading">Personal Settings</div>
    <div class="list-group">
      <a href="/settings/profile" class="list-group-item {!! (Request::is('*profile') ? 'selected' : '') !!}"><span class="glyphicon glyphicon-user"></span> Profile</a>
      <a href="/settings/account" class="list-group-item {!! (Request::is('*account') ? 'selected' : '') !!}"><span class="glyphicon glyphicon-cog"></span> Account</a>
      <!--<a href="/settings/notifications" class="list-group-item {!! (Request::is('*notifications') ? 'selected' : '') !!}">Notifications</a>-->
    </div>
  </div>

  @if(Session::has('company_id') && Session::get('company_join_type') == 'admin')
    <div class="panel panel-default">
      <div class="panel-heading">Company Settings</div>
      <div class="list-group">
        <a href="/settings/company" class="list-group-item {!! (Request::is('*company') ? 'selected' : '') !!}"><span class="glyphicon glyphicon-flag"></span> About {!! Session::get('company_name') !!}</a>
        <a href="/settings/company/preferences" class="list-group-item {!! (Request::is('*company/preferences') ? 'selected' : '') !!}"><span class="glyphicon glyphicon-tasks"></span> Preferences</a>
        <a href="/settings/company/users" class="list-group-item {!! (Request::is('*company/users') ? 'selected' : '') !!}"><span class="glyphicon glyphicon-briefcase"></span> Employees / Users</a>
      </div>
    </div>
  @endif
</div>
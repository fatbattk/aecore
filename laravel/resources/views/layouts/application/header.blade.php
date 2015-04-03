<script type="text/javascript">
  $(function(){
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>


<div class="navbar-header">
  @if (Auth::check())
    {{ link_to('/projects', '', array('class' => 'navbar-brand')) }}
  @else
    {{ link_to('/home', '', array('class' => 'navbar-brand')) }}
  @endif
</div>
<ul class="nav navbar-nav">
  <li><a href="/projects" class="{{ Request::is('projects*') ? 'active' : '' }}"><span class="glyphicon glyphicon-home"></span> Projects</a>
  <li><a href="/tasks" class="{{ Request::is('tasks*') ? 'active' : '' }}"><span class="glyphicon glyphicon-check"></span> Tasks</a>
</ul>
<ul class="nav navbar-nav pull-right">
  <li><a href="/profile/{{ Auth::user()->identifier }}" class="navbar-link" title="Profile" data-toggle="tooltip" data-placement="top"><img src="{{ Auth::user()->gravatar }}" class="avatar_xs" title="{{ Auth::user()->name }}" /><span class="navbar-username"> {{ Auth::user()->name }}</span></a></li>
  <li><a href="/settings/profile" class="{{ Request::is('settings*') ? 'active' : '' }}" title="Settings" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-cog" style="height:19px;top:3px;"></span></a></li>
  <li><a href="/logout" title="Log Out" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-log-out" style="height:19px;top:3px;"></span></a></li>
</ul>

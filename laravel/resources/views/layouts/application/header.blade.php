<script type="text/javascript">
  $(function(){
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>
  
<div class="navbar-header">
  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
  </button>
  @if (Auth::check())
    {!! link_to('/projects', '', array('class' => 'navbar-brand')) !!}
  @else
    {!! link_to('/home', '', array('class' => 'navbar-brand')) !!}
  @endif
</div>
<div class="collapse navbar-collapse" id="navbar-collapse">
  <ul class="nav navbar-nav">
    <li><a href="/projects" class="{!! Request::is('projects*') ? 'navbar-link-active' : 'navbar-link' !!}">Projects</a>
    <li><a href="/tasks" class="{!! Request::is('tasks*') ? 'navbar-link-active' : 'navbar-link' !!}">Tasks</a>
  </ul>
  <ul class="nav navbar-nav navbar-right">
    <li><a href="/profile/{!! Auth::user()->identifier !!}" class="navbar-link" title="Profile" data-toggle="tooltip" data-placement="top"><img src="{!! Auth::user()->gravatar !!}" class="avatar_nav" title="{!! Auth::user()->name !!}" /><span class="navbar-username"> {!! Auth::user()->name !!}</span></a></li>
    <li><a href="/settings/profile" class="{!! Request::is('settings*') ? 'navbar-link-active' : 'navbar-link' !!}" title="Settings" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-cog" style="height:19px;top:3px;"></span></a></li>
    <li><a href="/logout" class="navbar-link" title="Log Out" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-log-out" style="height:19px;top:3px;"></span></a></li>
  </ul>
</div>
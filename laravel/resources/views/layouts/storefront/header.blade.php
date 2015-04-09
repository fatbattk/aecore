<script type="text/javascript">
  $(function(){
    $('[data-toggle="tooltip"]').tooltip()
  });
</script>

<nav class="navbar navbar-fixed-top navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      
      @if (Auth::check())
        {!! link_to('projects', '', array('class' => 'navbar-brand')) !!}
      @else
        {!! link_to('home', '', array('class' => 'navbar-brand')) !!}
      @endif
    </div>
    <div class="collapse navbar-collapse" id="navbar-collapse">
      <ul class="nav navbar-nav navbar-right">
        @if (Auth::check())
          <li><a href="/logout" class="navbar-link" title="" data-toggle="tooltip" data-placement="bottom">Log Out</a></li> 
        @else
          <li><a href="/signup" class="navbar-link bold" title="It's easy!" data-toggle="tooltip" data-placement="bottom">Sign Up</a></li>
          <li><a href="/login" class="navbar-link" title="" data-toggle="tooltip" data-placement="bottom">Log In</a></li>
        @endif
      </ul>
    </div>
  </div>
</nav>
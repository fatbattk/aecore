<script type="text/javascript">
  //Start processing plans
  processSets();
</script>

<div class="modal-header">
  <h4 class="modal-title">Process Plan Sets</h4>
</div>
<div class="modal-body">
  <p class="text-muted" id="processtext"><img src="{!! URL::asset('images/icons/loader-infinity.gif') !!}"/> Our bots are hard at work processing your plans!</p>
  <div class="progress">
    <div class="progress-bar progress-bar-success" id="progressbar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
  </div>
  <p class="small center" id="navtext"><span class="glyphicon glyphicon-exclamation-sign"></span> Do not refresh or navigate way from this page.</p>
  <div class="alert alert-info" id="ready" role="alert" style="display:none;">
    <p><strong>Success!</strong> Your sheets are ready for publishing. <a href="/planroom/sheets/review" class="btn btn-info btn-sm btn-spacer-left"><span class="glyphicon glyphicon-eye-open"></span> Review & Publish</a></p>
  </div>
  <p class="text-muted center">Play some PAC-MAN while you wait! <span class="btn-link bold" onClick="$('#pacman').show();$(this).hide();" title="It's worth it!">Insert Coin</span></p>
  <div align="center" style="display:none;" id="pacman">
    <embed src="http://www.classicgamesarcade.com/games/pacman.swf" width="415px" height="500px" autostart="true" loop="false" controller="true"></embed>
  </div>
</div>
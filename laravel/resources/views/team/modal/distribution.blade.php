<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title">Manage Distribution Lists</h4>
</div>
<!-- Add collaborator form -->
<div id="collab_form">
  <div class="modal-body" style="padding:0;overflow:auto;">
    <div class="col-md-5 distributeColumn" id="distributeLists">
      @include('team.distribution.lists')
    </div>
    <div class="col-md-7 distributeColumn" id="distributeUsers">
      <h4 class="text-muted" style="text-align:center;margin-top:160px;font-family:'OpenSans';">Please Select<br>a Distribution List</h4>
    </div>
  </div>
  <div class="modal-footer" style="margin:0;">
    <button type="button" class="btn btn-default btn-spacer-left" data-dismiss="modal">Done</button>
  </div>
</div>
<script type="text/javascript">
  //Set loading zone
  function set_loading() {
    $('#button-zone').hide();
    $('#loading-zone').show();
  }
 
  //Toggle collaborator inputs
  function toggle_collab() {
    if($("#user_form").css('display') == "none") {
      $('#collab_form').hide();
      $('#user_form').show();
      $('#name').focus();
    } else {
      $('#user_form').hide();
      $('#collab_form').show();
      $('#term').focus();
    }
  }
  
  $(document).ready(function(){
    //Find users
    var NoResultsLabel = "No results found.";
    $('#term').autocomplete({
      source: function(request, response) {    
        $.ajax({ url: "/autocomplete/users",
          data: {term: $("#term").val()},
          dataType: "json",
          type: "POST",
          success: function(data){
            if(!data.length){
              var result = [{
                label: NoResultsLabel,
                title: '',
                value: response.term,
                file: ''
              }];
               response(result);
             } else {
              response(data);
            }
          }
        });
      },
      minLength:1,
      focus: function(event, ui) {
        if (ui.item.label === NoResultsLabel) {
          event.preventDefault();
        } else {
          $("#term").val(ui.item.label);
        }
        return false; // Prevent the widget from inserting the value.
      },
      change: function (event, ui) {
        if(!ui.item){
          $(event.target).val("");
        }
      },
      select: function(event, ui) {
        if (ui.item.label === NoResultsLabel) {
          $(event.target).val("");
        } else {
          $('#collaborator-list').append('<div class="user_tile pull-left" id="collab_tag_' + ui.item.identifier + '">' + ui.item.avatar + '<span class="glyphicon glyphicon-remove" onClick="$(\'#collab_tag_' + ui.item.identifier + '\').remove();$(\'#collab_' + ui.item.identifier + '\').remove();" title="Remove"></span><p class="line1">' + ui.item.label + '</p><p class="line2">' + ui.item.title + '</p></div>');
          $('#collaborator-list-data').append('<input type="hidden" id="collab_' + ui.item.identifier + '" name="user[]" value="' + ui.item.identifier + '" />');
          $(event.target).val("");
        }
        return false;// Prevent the widget from inserting the value.
      }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $('<li></li>')
        .append('<a>' + item.avatar + '<span class="bold" style="margin:0 0 2px 0;">' + item.label + '</span><br><span class="text-muted small" style="margin:0;">' + item.title + '</span>' )
        .appendTo(ul);
      };
  });
</script>
  
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title">Add Team Members</h4>
</div>
  <!-- Add collaborator form -->
  <div id="collab_form">
    <div class="modal-body">
      {!! Form::open(array('id'=>'add_team_form', 'url' => 'team/add', 'method' => 'post', 'class' => 'form-horizontal no-margin')) !!}
        <div class="form-group no-margin">
          <div class="col-sm-12">
            {!! Form::text('term', null, array('id'=>'term', 'class'=>'form-control', 'placeholder'=>'Search by name or company...', 'autofocus'=>'true')) !!}
            <p style="font-size:1em;margin:8px 0 0 0;" class="light">Can't find who you're looking for? <span class="btn-link" onClick="toggle_collab();">Invite a Person</span></p>
            <div id="collaborator-list" style="margin-top:5px;"></div>
            <div id="collaborator-list-data"></div>
          </div>
        </div>
      {!! Form::close() !!}
    </div>
    <div class="modal-footer" style="margin:0;">
      <div id="button-zone">
        <span class="btn btn-success" onClick="set_loading();$('#add_team_form').submit();" title="Add team members to project."><span class="glyphicon glyphicon-plus"></span> Add Members</span>
        <button type="button" class="btn btn-default btn-spacer-left" data-dismiss="modal">Cancel</button>
      </div>  
      <div id="loading-zone" style="display:none;"><img src="{!! URL::asset('images/icons/loader-infinity.gif') !!}"/> Please wait while the collaborators are added..</div>
    </div>
  </div>
  
  <!-- Add user form -->
  <div id="user_form" style="display:none;">
    {!! Form::open(array('id'=>'add_user_form', 'method' => 'post', 'class' => 'form-horizontal no-margin')) !!}
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('name', 'Name', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-9">
            {!! Form::text('name', null, array('id'=>'name', 'class'=>'form-control', 'placeholder'=>'Full Name', 'required'=>'true')) !!}
            <span id="name_error" class="text-danger">{!! $errors->first('name') !!}</span>
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('email', 'Email', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-9">
            {!! Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email', 'required'=>'true' )) !!}
            <span id="email_error" class="text-danger">{!! $errors->first('email') !!}</span>
          </div>
        </div>
        <div class="form-group no-margin">
          <div class="col-sm-offset-2 col-sm-9">
            <span class="btn btn-success" title="Invite & add to project." onClick="sendInvite('add_user_form');">Send Invite</span>
            <span class="btn btn-link" onClick="toggle_collab();">Cancel</span>
          </div>
        </div>
      </div>
    {!! Form::close() !!}
  </div>
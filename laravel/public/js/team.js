// Update a task
function sendInvite(form) {
  $.ajax({
    type: "POST",
    url: '/team/invite',
    data: $('#'+form).serialize()
  }).done(function(data) {
    if(data.fail) {
      $.each(data.errors, function(index, value) {
        var errorDiv = '#'+index+'_error';
        $(errorDiv).empty().append(value);
        $(errorDiv).show();
      });
    } else {
      $('#collaborator-list').append('<div class="user_tile pull-left" id="collab_tag_' + data[0].identifier + '">' + data[0].avatar + '<span class="glyphicon glyphicon-remove" onClick="$(\'#collab_tag_' + data[0].identifier + '\').remove();$(\'#collab_' + data[0].identifier + '\').remove();" title="Remove"></span><p class="line1">' + data[0].label + '</p><p class="line2">' + data[0].title + '</p></div>');
      $('#collaborator-list-data').append('<input type="hidden" id="collab_' + data[0].identifier + '" name="user[]" value="' + data[0].identifier + '" />');
      toggle_collab();
      $('#'+form).trigger("reset");
    }
  });
}

// Add a team member
function makeAdmin(identifier) { 
  $.ajax({
    type: "POST",
    url: '/team/admin/add',
    data: {identifier:identifier},
    success: function() {
      //do nothing
    }
  }).then(function() {
    window.location.href = '/team';
  });
}
// Remove a team member
function removeMember(identifier) { 
  $.ajax({
    type: "POST",
    url: '/team/remove',
    data: {identifier:identifier},
    success: function() {
      //do nothing
    }
  }).then(function() {
    window.location.href = '/team';
  });
}

// Add a distribution list
function addDistributionList(projectcode) {
  
  var listname = $('#list_name').val();
  
  $.ajax({
    type: "POST",
    url: '/team/list/add',
    data: {projectcode:projectcode, listname:listname},
    success: function(data) {
      $('#distributeLists').append('<div class="distributeTile pointer" id="list_'+data+'" onClick="showDistributionList(\'' + data + '\');$(\'.distributeTile\').removeClass(\'active\');$(this).addClass(\'active\');">'+listname+'</div>');
      $('#list_name').val('');
      $('#list_name').focus();
    }
  });
}

// Remove a distribution list
function removeDistributionList(code) {
  $.ajax({
    type: "POST",
    url: '/team/list/remove',
    data: {code:code},
    success: function() {
      $('#list_'+code).remove();
      $('#distributeUsers').html('<h4 class="text-muted" style="text-align:center;margin-top:160px;font-family:\'OpenSans\';">Please Select<br>a Distribution List</h4>')
    }
  });
}

function showDistributionList(listcode) {
  $.ajax({
    type:'GET',
    url: '/team/list/show/'+listcode,
    success: function(response) {
      $('#distributeUsers').html(response);
    }
  });
}

function toggleDistribution(status, listcode, identifier) {
  $.ajax({
    type: 'POST',
    url: '/team/userlist/toggle',
    data: {status:status, listcode:listcode, identifier:identifier},
    success: function() {
      //showDistributionList(listcode);
      if(status == 'active') {
        $('#onbutton_'+identifier).removeClass('btn-default');
        $('#onbutton_'+identifier).addClass('btn-success');
        $('#offbutton_'+identifier).removeClass('btn-danger');
        $('#offbutton_'+identifier).addClass('btn-default');
      } else {
        $('#onbutton_'+identifier).removeClass('btn-success');
        $('#onbutton_'+identifier).addClass('btn-default');
        $('#offbutton_'+identifier).removeClass('btn-default');
        $('#offbutton_'+identifier).addClass('btn-danger');
      }
    }
  });
}
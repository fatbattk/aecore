// Update a task
function task_update(code) {
  var task = $('#task-text-'+code).val();
  
  $.ajax({
    type: "POST",
    url: '/tasks/update',
    data: {code:code,type:'task',data:task},
    success: function() {
      //do nothing
    }
  });
}

function task_assign(code, user_id, name) {
  
  $('#assigned_to').html('<p class="form-control-static"><span class="loader-infinity"></span> Saving...</p>');  
  $('#assigned_to_new').hide();
  $('#assigned_to').show();
  
  $.ajax({
    type: "POST",
    url: '/tasks/update',
    data: {code:code, type:'user_id', data:user_id},
    success: function() {
      setTimeout(function() { 
        $('#assigned_to').html('<p class="form-control-static"><span class="label label-primary label-lg">' + name + ' <span class="glyphicon glyphicon-remove pointer small" onClick="$(\'#assigned_to\').hide();$(\'#assigned_to_new\').show();$(\'#term\').focus();" title="Remove user & reassign task."></span></span></p>');
      }, 600);
    }
  });
}

function task_follower_add(code, user_id, name) {  
  $.ajax({
    type: "POST",
    url: '/tasks/follower',
    data: {code:code, data:user_id, status:'active'},
    success: function() {
      $('#followers').append('<span id="follower-' + code + user_id + '"  class="label label-primary label-lg" style="display:inline-block;margin:5px 5px 0 0;">' + name + ' <span class="glyphicon glyphicon-remove pointer small" onClick="$(\'#follower-' + code + user_id + '\').remove();" title="Remove user & reassign task."></span></span>');
    }
  });
}

function task_follower_remove(code, user_id) {
  $.ajax({
    type: "POST",
    url: '/tasks/follower',
    data: {code:code, data:user_id, status:'disabled'},
    success: function() {
      $('#follower-' + code + user_id).remove();
    }
  });
}

function taskComment(code) {
  var comment = $('#comment').val();
  $.ajax({
    type: "POST",
    url: '/tasks/comment',
    data: {code:code,data:comment},
    success: function() {
      taskDetails(code);
    }
  });
}

function task_list_add(taskcode, listcode, list) {
  $.ajax({
    type: "POST",
    url: '/tasks/list',
    data: { taskcode:taskcode, listcode:listcode, status:'active'},
    success: function() {
      $('#lists').append('<span id="list-' + listcode + '"  class="label label-warning label-lg" style="display:inline-block;margin:5px 5px 0 0;">' + list + ' <span class="glyphicon glyphicon-remove pointer small" onClick="task_list_remove(\'' + taskcode + '\', \'' + listcode + '\');" title="Remove user & reassign task."></span></span>');
    }
  });
}

function task_list_remove(taskcode, listcode) {
  $.ajax({
    type: "POST",
    url: '/tasks/list',
    data: { taskcode:taskcode, listcode:listcode, status:'disabled'},
    success: function() {
      $('#list-' + listcode).remove();
    }
  });
}

// Complete a task
function task_complete(code) {
  
  $('#task-checkbox-'+code).toggleClass('taskline-checkbox taskline-checkbox-complete');
  $('#task-checkbox-'+code).attr('onClick', 'task_open(\''+code+'\');');
  
  if($('#task-details').css('display') == 'block'){
    $('#task-checkbox-info-'+code).toggleClass('taskline-checkbox taskline-checkbox-complete');
    $('#task-checkbox-info-'+code).attr('onClick', 'task_open(\''+code+'\');');
  }
  
  $('#task-text-'+code).addClass('strike');
  $.ajax({
    type: "POST",
    url: '/tasks/complete',
    data: {code:code},
    success: function() {
      //do nothing
    }
  });
}

// Reopen a task
function task_open(code) {
  $('#task-checkbox-'+code).toggleClass('taskline-checkbox taskline-checkbox-complete');
  $('#task-checkbox-'+code).attr('onClick', 'task_complete(\''+code+'\');');
  
  if($('#task-details').css('display') == 'block'){
    $('#task-checkbox-info-'+code).toggleClass('taskline-checkbox taskline-checkbox-complete');
    $('#task-checkbox-info-'+code).attr('onClick', 'task_complete(\''+code+'\');');
  }
      
  $('#task-text-'+code).removeClass('strike');
  //Focus, clear and replace text to move cursor to end
//  $('#task-text-'+code).focus();
//  var tmpStr = $('#task-text-'+code).val();
//  $('#task-text-'+code).val('');
//  $('#task-text-'+code).val(tmpStr);
  
  $.ajax({
    type: "POST",
    url: '/tasks/open',
    data: {code:code},
    success: function() {
    }
  });
}

// Update a task
function task_date(code) {
  $('#loader-line-date').html('<p style="margin:5px 0 0 0;padding:0;"><span class="loader-infinity"></span> Saving...</p>');
  var due_at = $('#due_at').val();
  $.ajax({
    type: "POST",
    url: '/tasks/update',
    data: {code:code,type:'due_at',data:due_at},
    success: function() {
      setTimeout(function() { 
        $('#loader-line-date').html(''); 
      }, 600);
    }
  });
}

function task_remove_attachment(taskcode, file_id) {
  $.ajax({
    type: "POST",
    url: '/tasks/attachment/remove/'+taskcode,
    data: { file_id:file_id },
    success: function() {
      $('#attachment-' + file_id).remove();
    }
  });
}

function remove_list(listcode) {
      
  $.ajax({
    type: "POST",
    url: '/tasks/list/remove',
    data: { listcode:listcode },
    success: function() {
      $('#li-list-' + listcode).hide();
    }
  }).then(function() {
    window.location.href = '/tasks';
  });
}

function taskDetails(code) {
  $.ajax({
    type:'GET',
    url: '/tasks/details/'+code,
    success: function(response) {
      $('#task-details').html(response);
      if($('#task-details').css('display') == 'none'){
        $('#task-list').css('right', '460px');
        $('#task-details').show();
      }
    }
  });
}
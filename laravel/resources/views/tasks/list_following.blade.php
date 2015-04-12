@extends('layouts.application.main_wide')
@section('content')
  <script type="text/javascript" src="{!! URL::asset('/js/tasks.js') !!}"></script>
  <script type="text/javascript">
    $(function(){
      $('input[type=text][name=list_name]').tooltip({
        placement: "bottom",
        trigger: "focus"
      });    
    });
  </script>

  <div class="sidebar-wrapper" id="projectnav">
    <ul class="sidebar-nav">
      <li class="nav-header"><span class="glyphicon glyphicon-list"></span> My Lists</li>
      <li>{!! link_to('tasks', 'All Tasks', array('class'=>(Request::is('tasks') ? 'active' : '') )) !!}</li>
      @foreach($lists as $list)
        <li id="li-list-{!! $list->listcode !!}" onmouseover="$('#li-list-remove-<?php echo $list->listcode; ?>').show();" onmouseout="$('#li-list-remove-<?php echo $list->listcode; ?>').hide();"><a id="li-a-list-{!! $list->listcode !!}" href="/tasks/{!! $list->listcode !!}" class="{!! Request::is('*'.$list->listcode) ? 'active' : '' !!}">{!! $list->list !!} <span id="li-list-remove-{!! $list->listcode !!}" class="glyphicon glyphicon-remove-sign pull-right text-muted text-hover-danger" title="Remove list." style="margin-top:2px;display:none;" onClick="remove_list('<?php echo $list->listcode; ?>');event.preventDefault();"></span></a></li>
      @endforeach
      <div class="form-group" id="list_name" style="margin:6px 10px;display:none;">
        {!! Form::open(array('url' => '/tasks/list/create', 'method' => 'post', 'class' => 'form-horizontal')) !!}
        {!! Form::text('list_name', null, array('id'=>'list_name_input', 'class' => 'form-control', 'placeholder' => 'List name', 'autocomplete'=>'off', 'title'=>'Press Enter to submit.' )) !!}
        {!! Form::close() !!}       
      </div>
      <li>
        <span class="btn btn-link-light btn-xs" id="new-list-btn" title="Add a new list." style="padding:6px 0;margin-left:15px;" onClick="$('#list_name').show();$('#list_name_input').focus();"><span class="glyphicon glyphicon-plus"></span> New List</span>
      </li>
      @if(count($following) > 0)
        <br>
        <?php $useravatar = new App\Models\Useravatar; ?>
        <li class="nav-header"><span class="glyphicon glyphicon-eye-open"></span> Following</li>
        @foreach($following AS $follow_data)
          <li id="li-list-{!! $follow_data->identifier !!}"><a href="/tasks/following/{!! $follow_data->identifier !!}" class="{!! Request::is('*'.$follow_data->identifier) ? 'active' : '' !!}"><img src="{!! $useravatar->getUserAvatar($follow_data->user_id, 'sm') !!}" class="avatar_xs" />{!! $follow_data->name !!}</a></li>
        @endforeach
      @endif
    </ul>
  </div>

  <div class="task-list-wrapper" id="task-list">
    <div class="pagehead">
      <div class="container-fluid">
        @if($completed_count > 0)
          <a href="/tasks/list/refresh" class="btn btn-sm btn-warning pull-right btn-spacer-left" title="Refresh list to clear completed tasks."><span class="glyphicon glyphicon-refresh"></span> Clear Completed</a>
        @endif
        
        <div class="btn-group pull-right">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle btn-spacer-left" data-toggle="dropdown">{!! Session::get('filter_text') !!} <span class="caret"></span></button>
          <ul class="dropdown-menu" role="menu">
            @if(Session::get('filter_text') == "Open Tasks")
              <li><a href="{!! '/tasks/' . Session::get('listcode') . '?filter=complete' !!}">Completed Tasks</a>
            @else
              <li><li><a href="{!! '/tasks/' . Session::get('listcode') . '?filter=active' !!}">Open Tasks</a>
            @endif
          </ul>
        </div>
        
        <div class="btn-group pull-right task-project-filter">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">{!! $listname !!} <span class="caret"></span></button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/tasks">All Tasks</a></li>
            @foreach($lists as $list)
              <li><a href="/tasks/{!! $list->listcode !!}">{!! $list->list !!}</a></li>
            @endforeach            
          </ul>
        </div>
        
        <h1>{!! $listname !!}</h1>
      </div>
    </div>

    @if(count($theirtasks) == 0)
      <div class="alert alert-warning no-margin" style="margin:5px 15px 10px 15px;">
        <p><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>No {!! Session::get('filter_text') !!} found in "{!! $listname !!}".</strong></p>
        <p>To get started, enter a task above.</p>
      </div>
    @endif

    @foreach($theirtasks as $theirtask)
      <div class="taskline col-md-12" id="taskline-<?php echo $theirtask->code; ?>" onmouseover="$('#expand-<?php echo $theirtask->code; ?>').addClass('taskline-button-gray');" onmouseout="$('#expand-<?php echo $theirtask->code; ?>').removeClass('taskline-button-gray');">
        <?php if($theirtask->status == 'complete') { ?>
          <span class="taskline-checkbox-complete" id="task-checkbox-<?php echo $theirtask->code; ?>" title="Reopen this task." onClick="task_open('<?php echo $theirtask->code; ?>');"></span>
        <?php } else { ?>
          <span class="taskline-checkbox" id="task-checkbox-<?php echo $theirtask->code; ?>" title="Mark as complete." onClick="task_complete('<?php echo $theirtask->code; ?>');"></span>  
        <?php } ?>
          <div class="btn-group task-btn-group">
            <button data-toggle="dropdown" class="btn btn-<?php echo $theirtask->priority; ?> dropdown-toggle task-priority-tag" title="Change task priority." type="button"><span class="caret" style="margin-top:-7px;"></span></button>
            <ul class="dropdown-menu task-priority-list">
              <li><a href="{!! URL::to('tasks/priority/3/' . $theirtask->code) !!}"><span class="label label-danger">High Priority</span></a></li>
              <li><a href="{!! URL::to('tasks/priority/2/' . $theirtask->code) !!}"><span class="label label-warning">Medium Priority</span></a></li>
              <li><a href="{!! URL::to('tasks/priority/1/' . $theirtask->code) !!}"><span class="label label-info">Low Priority</span></a></li>
            </ul>
          </div>
        <div class="taskline-input-wrapper">
          <?php if($theirtask->due_at != "") { ?><span id="task-date-<?php echo $theirtask->code;?>" class="task_date"><?php echo $theirtask->due_at; ?></span><?php } ?>
          <input type="text" class="form-control taskline-input <?php if($theirtask->status == 'complete') { echo 'strike'; } ?>" id="task-text-<?php echo $theirtask->code;?>" value="<?php echo htmlspecialchars($theirtask->task); ?>" onFocus="$('#taskline-<?php echo $theirtask->code;?>').addClass('taskline-active');taskDetails('<?php echo $theirtask->code;?>');" onBlur="task_update('<?php echo $theirtask->code;?>');$('#taskline-<?php echo $theirtask->code;?>').removeClass('taskline-active');" onkeyup="$('#task-text-info').html(this.value);"/>
        </div>
      </div>
    @endforeach
  </div>

  <div class="task-info-wrapper" id="task-details">
  </div>
@stop
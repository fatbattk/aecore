@extends('layouts.application.main_wide')
@extends('layouts.application.project_nav')
@section('content')

<script type="text/javascript" src="{!! URL::asset('js/team.js') !!}"></script>
<div class="page-wrapper">
  <div class="pagehead">
    <div class="container-fluid">
      <span class="btn btn-primary btn-sm pull-left toggle-nav" style="margin-right:10px;padding:7px;" onClick="$('#projectnav').toggle();"><span class="glyphicon glyphicon-menu-hamburger"></span></span>
      <a class="btn btn-default btn-sm pull-right btn-spacer-left" href="/team/modal/help" data-target="#modal" data-toggle="modal" title="How does this work?">Help</a>
      <a href="{!! URL::to('pdf/team') !!}" class="btn btn-default btn-sm pull-right btn-spacer-left" target="_blank" title="Print to PDF."><span class="glyphicon glyphicon-print"></span> Team Directory</a>
      @if(Session::get('project_access') == 'admin')       
        <a class="btn btn-warning btn-sm pull-right btn-spacer-left" href="/team/modal/distribution" data-target="#modal" data-toggle="modal" title="Manage your distribution lists."><span class="glyphicon glyphicon-th-list"></span> Distribution</a>
        <a class="btn btn-success btn-sm pull-right" href="/team/modal/add" data-target="#modal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add Members</a>
      @endif
      <h1>Project Team</h1>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <?php $useravatar = new App\Models\Useravatar; ?>
      @foreach($members AS $member)
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="panel" onmouseover="$('#member_options_<?php echo $member->identifier; ?>').show();" onmouseout="$('#member_options_<?php echo $member->identifier; ?>').hide();">
          <div class=" panel-body team-tile">
            @if($member->projectuser_status == 'invited')
              <span class="text-success small bold pull-right btn-spacer-left">Invited</span>
            @endif
            @if($member->projectuser_access == 'admin')
              <span class="text-warning small bold pull-right btn-spacer-left"><span class="glyphicon glyphicon-tower"></span></span>
            @endif            
            <span id="member_options_{!! $member->identifier !!}" class="pull-right" style="display:none;">
              @if(Session::get('project_access') == 'admin' && $member->projectuser_access != 'admin' && $member->projectuser_status != 'invited')
                <span class="btn-link-light small" title="Make admin." onClick="makeAdmin('<?php echo $member->identifier; ?>');">+Admin</span>
              @endif
              @if(Session::get('project_access') == 'admin')
                <span class="btn-link-light small" title="Remove from project." onClick="removeMember('<?php echo $member->identifier; ?>');"><span class="glyphicon glyphicon-trash"></span></span>
              @endif
            </span>
            <img src="{!! $useravatar->getUserAvatar($member->id, 'lg') !!}" class="avatar_lg" />
            <p><a href="/profile/{!! $member->identifier !!}" style="font-size:1.2em;">{!! $member->name !!}</a></p>
            <p class="text-muted small bold">{!! $member->title . $member->company_name !!}</p>
            <p class="text-muted small">{!! HTML::mailto($member->email, null, array('class'=>'btn-link-light')) !!}</p>
            <p class="text-muted small">{!! $member->mobile !!}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@stop
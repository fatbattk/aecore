@extends('layouts.application.main')
@section('content')
<div class="pagehead">
  <div class="container">
    <a class="btn btn-sm btn-success pull-right bold"  href="/projects/new" title="Create a new project."><span class="glyphicon glyphicon-plus"></span> New Project</a>
    <h1>My Projects</h1>
  </div>
</div>

<div class="container">
  @if(Session::has('success'))
    <script type="text/javascript" charset="utf-8">
      setTimeout(function() {
        $("#deletesuccess").fadeOut("slow");
      }, 2500);
    </script>
    <div class="alert alert-success" id="deletesuccess"><span class="glyphicon glyphicon-ok"></span> {!! Session::get('success') !!}</div>
  @endif

  @if(count($projects) == 0)
     <div class="alert alert-info">
      <p class="bold">No projects were found.</p>
      <p>Try changing your filter or create a <a href="/projects/new" class="btn btn-success btn-xs bold"><span class="glyphicon glyphicon-plus"></span> New Project</a> to get started.</p>
    </div>
  @else
    <div class="panel">
      <table class="table table-hover table-sortable">
        <thead>
          <tr>
            <th>Job No.</th>
            <th>Name</th>
            <th>Size</th>
            <th>Value</th>
            <th>Status</th>
            <th>Date Start</th>
            <th>Date Finish</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($projects AS $project)
          <tr onClick="{!! 'location.href=\'projects/launch/' . $project->code . '\'' !!}" class="pointer">
            <td>{!! $project->number !!}</td>
            <td>{!! $project->name !!}</td>
            <td>{!! $project->size . ' ' . $project->size_unit !!}</td>
            <td>{!! $project->value !!}</td>
            <td>{!! $project->status !!}</td>
            <td>{!! $project->start !!}</td>
            <td>{!! $project->finish !!}</td>
            <td>{!! link_to('projects/edit/'.$project->code, 'Edit', array('class' => 'btn btn-xs btn-default')) !!}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@stop
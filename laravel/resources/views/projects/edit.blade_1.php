@extends('layouts.application.main')
@section('content')

<script type="text/javascript" src="{!! URL::asset('js/jquery.currency.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/countries.js') !!}"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#value').blur(function() {
      $('#value').currency();
    });
    $('#size').blur(function() {
      $('#size').currency();
	  });
    //Insert country & state
    print_country("country", "state", "{!! $project->country !!}", "{!! $project->state !!}");
    
    //Date selectors
    $("#start").datepicker({
       changeMonth: true,
       changeYear: true
    });
    $("#finish").datepicker({
       changeMonth: true,
       changeYear: true
    });
    $("#archive").datepicker({
       changeMonth: true,
       changeYear: true
    });
  });
  
  function update_size_unit(unit) {
    if(unit == "feet") {
      $('#size_unit_button_text').text('SQ FT');
      $('#size_unit').val('feet');
    } else if (unit == "meters") {
      $('#size_unit_button_text').text('SQ M');
      $('#size_unit').val('meters');
    }
  }
</script>

<div class="pagehead">
  <div class="container">
    <h1>Edit Project</h1>
  </div>
</div>
<div class="container">
  {!! Form::open(array('url' => 'projects/edit/' . $project->code . '/save', 'method' => 'post', 'class' => 'form-horizontal')) !!}
    <div class="panel panel-default">
      <div class="panel-heading">Basic Information</div>
      <div class="panel-body">
        <div class="form-group">
          {!! Form::label('status', 'Status', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-4 col-lg-3">
            {!! Form::select('status', array(
                'Bid' => 'Bid',
                'Pre-construction' => 'Pre-construction',
                'Under Construction' => 'Under Construction',
                'Close Out' => 'Close Out',
                'Archived' => 'Archived'
              ), $project->status, array('class'=>'form-control', 'required'=>'true', $disabled))
            !!}
            <span class="text-danger">{!! $errors->first('status') !!}</span>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('archive', 'Date Archived', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-4 col-lg-3">
            {!! Form::text('archive', $project->archive, array('id'=>'archive', 'style'=>'position:relative;z-index:10;', 'class' => 'form-control', 'placeholder' => 'Date archived...', $disabled )) !!}
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('type', 'Type', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-4 col-lg-3">
            {!! Form::select('type', array(
                'Core & Shell' => 'Core & Shell',
                'Commercial' => 'Commercial',
                'Clerical' => 'Clerical',
                'Data Center' => 'Data Center',
                'Education' => 'Education',
                'Flatwork' => 'Flatwork',
                'Medical & Health Care' => 'Medical & Health Care',
                'Heavy Civil' => 'Heavy Civil',
                'High-tech' => 'High-tech',
                'Industrial' => 'Industrial',
                'Office' => 'Office',
                'Parking Structure' => 'Parking Structure',
                'Parks & Recreation' => 'Parks & Recreation',
                'Residential' => 'Residential',
                'Retail' => 'Retail',
                'Service Work' => 'Service Work',
                'Seismic Retrofit' => 'Seismic Retrofit',
                'Tenant Improvement' => 'Tenant Improvement',
                'Tilt-Up' => 'Tilt-Up',
                'Transportation' => 'Transportation'
              ), $project->type, array('class'=>'form-control', 'required'=>'true', $disabled))
            !!}
            <span class="text-danger">{!! $errors->first('type') !!}</span>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('number', 'Job Number', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-4 col-lg-3">
            {!! Form::text('number', $project->number, array('class' => 'form-control', 'placeholder' => 'ex. 1357', 'required'=>'true' )) !!}
            <span class="text-danger">{!! $errors->first('number') !!}</span>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('name', 'Name', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6">
            {!! Form::text('name', $project->name, array('class' => 'form-control', 'placeholder' => 'ex. Park Plaza TI', 'required'=>'true', $disabled )) !!}
            <span class="text-danger">{!! $errors->first('name') !!}</span>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('start', 'Schedule', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-3">
            <div class="input-group">
              <span class="input-group-addon"><span class="glyphicon glyphicon-star-empty"></span></span>
              {!! Form::text('start', $project->start, array('id'=>'start', 'class' => 'form-control', 'placeholder' => 'Date Start', 'required'=>'true' )) !!}
            </div>
          </div>
          <div class="col-sm-3">
            <div class="input-group">
              <span class="input-group-addon"><span class="glyphicon glyphicon-star"></span></span>
              {!! Form::text('finish', $project->finish, array('id'=>'finish', 'class' => 'form-control', 'placeholder' => 'Date Finish', 'required'=>'true' )) !!}
            </div>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('value', 'Value', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-3">
            <div class="input-group">
              <span class="input-group-addon">$</span>
              {!! Form::text('value', $project->value, array('id'=>'value', 'class' => 'form-control', 'placeholder' => 'ex. 155,000' )) !!}
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label" for="size">Size</label>
          <div class="col-sm-3">
            <div class="input-group">
              {!! Form::text('size', $project->size, array('id'=>'size', 'class' => 'form-control', 'placeholder' => 'ex. 24,500', $disabled)) !!}
              {!! Form::hidden('size_unit', $project->size, array('id'=>'size_unit', $disabled)) !!}
              <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" {!! $disabled !!}"><span id="size_unit_button_text">{!! $project->size_unit_text !!}</span> <span class="caret"></span></button>
                <ul class="dropdown-menu pull-right">
                  <li><span id="feet" onclick="update_size_unit('feet');">Square Feet</span></li>
                  <li><span id="meters" onclick="update_size_unit('meters');">Square Meters</span></li>
                </ul>
              </div><!-- /btn-group -->
            </div><!-- /input-group -->
          </div>
        </div>

        <div class="form-group no-margin">
          {!! Form::label('description', 'Description', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6">
            {!! Form::textarea('description', $project->description, array('class' => 'form-control', 'rows'=>'4', 'placeholder' => 'Project Description...', $disabled )) !!}
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Project Location</div>
      <div class="panel-body">
        <div class="form-group">
          {!! Form::label('street', 'Street', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-5">
            {!! Form::text('street', $project->street, array('class' => 'form-control', 'placeholder' => 'Street', $disabled )) !!}
            <span class="text-danger">{!! $errors->first('street') !!}</span>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('city', 'City', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-5">
            {!! Form::text('city', $project->city, array('class' => 'form-control', 'placeholder' => 'City', $disabled )) !!}
            <span class="text-danger">{!! $errors->first('city') !!}</span>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('country', 'Country', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-5">
            {!! Form::select('country', array(
                '' => 'Select Country'
              ), null, array('class' => 'form-control', 'onChange' => 'print_state(\'state\', this.selectedIndex)', $disabled))
            !!}
            <span class="text-danger">{!! $errors->first('country') !!}</span>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('state', 'State', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-5">
            {!! Form::select('state', array(
                '' => 'Select State'
              ), null, array('class' => 'form-control', $disabled))
            !!}
            <span class="text-danger">{!! $errors->first('state') !!}</span>
          </div>
        </div>

        <div class="form-group no-margin">
          {!! Form::label('zipcode', 'Zip Code', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-6 col-md-5">
            {!! Form::text('zipcode',$project->zipcode, array('class' => 'form-control', 'placeholder' => 'Zip Code', $disabled )) !!}
            <span class="text-danger">{!! $errors->first('zipcode') !!}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Project Settings</div>
      <div class="panel-body">
        <div class="form-group">
          {!! Form::label('submittal_code', 'Submittal Numbers', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-4">
            {!! Form::select('submittal_code', array(
                'csicode' => 'CSI Master Format 2004',
                'costcode' => 'Cost Codes'
              ), $project->submittal_code, array('class'=>'form-control', 'required'=>'true', $disabled))
            !!}
          </div>
        </div>
        <div class="form-group no-margin">
          <div class="col-sm-offset-2 col-sm-6 col-md-4 col-lg-3">
            {!! Form::submit('Save', array('class' => 'btn btn-success')) !!}
            {!! link_to('projects', 'Cancel', array('class' => 'btn btn-default btn-spacer-left')) !!}
          </div>
        </div>
      </div>
    </div>
      
  {!! Form::close() !!}
</div>    
@stop
@extends('layouts.application.main')
@section('content')
 
  @if(!Session::has('company_id'))
  <script type="text/javascript">
    $(document).ready( function() {
      var NoResultsLabel = "No Results - Add a New Company";
      $('#term').autocomplete({
        source: function(request, response) {    
          $.ajax({ url: "/autocomplete/companies",
            data: {term: $("#term").val()},
            dataType: "json",
            type: "POST",
            success: function(data){
              if(!data.length){
                var result = [{
                  label: NoResultsLabel,
                  name: NoResultsLabel,
                  value: response.term,
                  location: '',
                  logo: ''
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
            $.ajax({
              url: "/settings/account/company/createform",
              type: "get",
              success: function(view) {
                $('#create_company_wrapper').html(view);
              }
            });
            
            //Insert country & state
            //print_country("company_country_new", "company_state_new", "United States", "");  
          } else {
            $("#company_id").val(ui.item.value);
            $("#join_company_form").submit();
          }
          return false;// Prevent the widget from inserting the value.
        }
      }).data("ui-autocomplete")._renderItem = function(ul, item) {
          return $('<li></li>')
          .append('<a>' + item.logo + '<span class="bold" style="margin:0;">' + item.label + '</span><br><span class="light" style="margin:0;">' + item.location + '</span></a>' )
          .appendTo(ul);
        }; 
    });
  </script>
  @endif
  
  <div class="pagehead">
    <div class="container">
      <h1>Settings / Account</h1>
    </div>
  </div>
  
  <div class="container">  
    <div class="row">  
      @include('settings.nav')
      <div class="col-sm-8 col-md-9">
        
        @if (Session::has('accountUpdateSuccess'))
          <script type="text/javascript" charset="utf-8">
            setTimeout(function() {
              $("#deletesuccess").fadeOut("slow");
            }, 3000);
          </script>
          <div class="alert alert-success" id="deletesuccess"><span class="glyphicon glyphicon-ok"></span> {!! Session::get('accountUpdateSuccess') !!}</div>
        @endif
        
        @if (Session::has('accountUpdateError'))
          <script type="text/javascript" charset="utf-8">
            setTimeout(function() {
              $("#deleteerror").fadeOut("slow");
            }, 3000);
          </script>
          <div class="alert alert-danger" id="deleteerror"><span class="glyphicon glyphicon-warning-sign"></span> {!! Session::get('accountUpdateError') !!}</div>
        @endif
        
        <div class="panel panel-default">
          <div class="panel-heading">Change Password</div>
          <div class="panel-body">
            {!! Form::open(array('url' => 'settings/account/password', 'method' => 'post', 'class' => 'form-horizontal')) !!}
              <div class="form-group">
                {!! Form::label('old_password', 'Old Password', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::password('old_password', array('class' => 'form-control', 'placeholder' => 'Old Password' )) !!}
                  <span class="text-danger">{!! $errors->first('old_password') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('new_password', 'New Password', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::password('new_password', array('class' => 'form-control', 'placeholder' => 'New Password' )) !!}
                  <span class="text-danger">{!! $errors->first('new_password') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('confirm_new_password', 'Confirm', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::password('confirm_new_password', array('class' => 'form-control', 'placeholder' => 'Confirm New Password' )) !!}
                  <span class="text-danger">{!! $errors->first('confirm_new_password') !!}</span>
                </div>
              </div>

              <div class="form-group no-margin">
                <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
                  {!! Form::submit('Update Password', array('class' => 'btn btn-success')) !!}
                </div>
              </div>
            {!! Form::close() !!}
          </div>
        </div>
        
        <div class="panel panel-default">
          <div class="panel-heading">Company</div>
          <div class="panel-body">            
            @if(Session::has('company_id'))
              <div class="form-horizontal">
                <div class="form-group">
                  {!! Form::label('term', 'My Company', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                  <div class="col-md-9 col-lg-8">
                    <p class="form-control-static">
                      <strong>{!! Session::get('company_name'); !!}</strong><br>
                      @if($companydata->street != null)
                        {!! $companydata->street !!}<br>
                      @endif
                      @if($companydata->city != "")
                        {!! $companydata->city . ', ' . $companydata->state . ' ' . $companydata->zipcode !!}<br>
                      @endif
                      @if($companydata->url != "")
                        {!! link_to('//'.$companydata->url, $companydata->url, array('class' => '', 'target'=>'_blank')) !!}
                      @endif
                    </p>
                  </div>
                </div>
                {!! Form::open(array('url' => 'settings/account/company/leave', 'method' => 'post')) !!}
                  <div class="form-group">
                    {!! Form::label('leave', 'Leave Company', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                    <div class="col-md-9 col-lg-8">
                      {!! Form::text('leave', null, array('class' => 'form-control', 'placeholder' => 'Type "LEAVE" here...', 'autocomplete'=>'off' )) !!}
                      <span class="text-danger">{!! $errors->first('leave') !!}</span>
                    </div>
                  </div>
                  <div class="form-group no-margin">
                    <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
                      {!! Form::submit('Leave Company', array('class' => 'btn btn-danger')) !!}
                    </div>
                  </div>
                {!! Form::close() !!}
              </div>
            @else
              <div id="create_company_wrapper">
                <div class="form-horizontal">
                  <div class="form-group no-margin">
                    <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
                      <p class="form-control-static">A company has its own set of users, projects, settings & billing details.</p>
                    </div>
                  </div>
                  <div class="form-group no-margin">
                    {!! Form::label('term', 'Join Company', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                    <div class="col-md-9 col-lg-8">
                      {!! Form::text('term', null, array('class' => 'form-control', 'placeholder' => 'Search by company name...', 'autocomplete'=>'off' )) !!}
                      <span class="text-muted small" style="margin-top:3px;">You can leave this company later if you need to.</span>
                    </div>
                  </div>
                </div>
                {!! Form::open(array('id'=>'join_company_form', 'class'=>'no-margin', 'url' => 'settings/account/company/join', 'method' => 'post')) !!}
                {!! Form::hidden('company_id', null, array('id'=>'company_id')) !!}
                {!! Form::close() !!}
              </div>
            @endif            
          </div>
        </div>
        
        <div class="panel panel-danger">
          <div class="panel-heading bold">Delete Account</div>
          <div class="panel-body">
            {!! Form::open(array('url' => 'settings/account/delete', 'method' => 'post', 'class' => 'form-horizontal')) !!}
            
            <div class="form-group">
              {!! Form::label('delete', 'Confirm', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::text('delete', null, array('class' => 'form-control', 'placeholder' => 'Type "DELETE" here...', 'autocomplete'=>'off' )) !!}
                <span class="text-danger">{!! $errors->first('delete') !!}</span>
              </div>
            </div>
            <div class="form-group no-margin">
              <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
                {!! Form::submit('Delete Account', array('class' => 'btn btn-danger')) !!}
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
        
      </div>
    </div>
  </div>
@stop
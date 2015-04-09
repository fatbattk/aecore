@extends('layouts.application.main')
@section('content')

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

  <div class="container">
    <div class="col-md-3"></div>
    <div class="col-md-6">    
      <div class="form-group">
        <div class="col-md-12" style="margin-bottom:15px;">
          <h3>Welcome!</h3>
          <p class="text-muted">To begin using Aecore's project management tools, you'll need to join a company. You can search for an existing company or add your own.</p>
          <p class="text-muted small">A company has its own set of users, projects, settings & billing details.</p>
        </div>
      </div>
      <div id="create_company_wrapper">
        <div class="form-group">
          {!! Form::label('term', 'Find and join your company', array('class' => 'col-md-12 control-label')) !!}
          <div class="col-md-12">
            {!! Form::text('term', null, array('style'=>'margin-bottom:3px;', 'class' => 'form-control', 'placeholder' => 'Search by company name...', 'autocomplete'=>'off', 'autofocus'=>true )) !!}
            <span class="text-muted small">You can leave this company later if you need to.</span>
          </div>
        </div>
        {!! Form::open(array('id'=>'join_company_form', 'class'=>'no-margin', 'url' => 'settings/account/company/join', 'method' => 'post')) !!}
        {!! Form::hidden('company_id', null, array('id'=>'company_id')) !!}
        {!! Form::close() !!}
      </div>
    </div>
  </div>
@stop
@extends('layouts.application.main_wide')
@extends('layouts.application.project_nav')
@section('content')

<script type="text/javascript" src="{!! URL::asset('js/planroom.js') !!}"></script>

<script type="text/javascript">
  $(document).ready(function(){
    //Find assign users
    var NoResultsLabel = "No results found.";
    $('#sheetlookup').autocomplete({
      source: function(request, response) {    
        $.ajax({ url: "/autocomplete/plansheets",
          data: {term: $("#sheetlookup").val()},
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
          $("#sheetlookup").val(ui.item.label);
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
          window.location.href = '/planroom/sheet/'+ui.item.value;
        }
        return false;// Prevent the widget from inserting the value.
      }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $('<li></li>')
        .append('<a href="/planroom/sheet/' + item.value + '">' + item.label + '</a>' )
        .appendTo(ul);
      };
  });
</script>

<div class="page-wrapper">
  <div class="pagehead">
    <div class="container-fluid">
      <span class="btn btn-primary pull-left toggle-nav" style="margin-right:10px;padding:7px;" onClick="$('#projectnav').toggle();"><span class="glyphicon glyphicon-menu-hamburger"></span></span>
      <a href="{!! URL::to('pdf/drawinglog') !!}" class="btn btn-default btn-sm pull-right btn-spacer-left" target="_blank" title="Print to PDF."><span class="glyphicon glyphicon-print"></span> Drawing Log</a>
      <a href="planroom/modal/upload" class="btn btn-success btn-sm pull-right" data-target="#modal" data-toggle="modal"><span class="glyphicon glyphicon-cloud-upload"></span> Upload Plans</a>    
      <h1>Plan Room</h1>
    </div>
  </div>

  <div class="container-fluid">
    <div class="form-inline" style="margin-bottom:15px;">
      <span class="text-muted">Filters:</span>
      <div class="btn-group btn-spacer-left">
        <button type="button" style="max-width:200px;" class="btn btn-default dropdown-toggle" data-toggle="dropdown">{!! str_limit(Session::get('set_text'), $limit = '21', $end = '...') !!} <span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu">
          <li>{!! HTML::link('/planroom?set=&discipline=' . Session::get('discipline_text'), 'Current Set') !!}</li>
          @foreach($sets AS $set)
            <li>{!! HTML::link('/planroom?set=' . $set->set_code . '&discipline=' . Session::get('discipline_text'), date('m-d-Y', strtotime($set->set_date)) . ' ' . $set->set_name) !!}</li>
          @endforeach
        </ul>
      </div>
      <div class="btn-group btn-spacer-left">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">{!! Session::get('discipline_text') !!} <span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu">
          <li>{!! HTML::link('/planroom?set=' . Session::get('set_code') . '&discipline=', 'All Disciplines') !!}</li>
          @foreach($disciplines AS $discipline)
            <li>{!! HTML::link('/planroom?set=' . Session::get('set_code') . '&discipline='.$discipline->sheet_discipline, $discipline->sheet_discipline) !!}</li>
          @endforeach
        </ul>
      </div>
      {!! Form::text('sheetlookup', null, array('id'=>'sheetlookup', 'style'=>'width:250px;', 'class'=>'form-control btn-spacer-left', 'placeholder'=>'Search for sheet')) !!}
    </div>
    
    @if(Session::get('unprocessed_plansets') == true)
      <div class="alert alert-warning" role="alert">
        <p>Your PDFs are ready for processing. Once started, please do not refresh the page. <a href="/planroom/modal/process" class="btn btn-warning btn-sm btn-spacer-left" data-target="#modal" data-toggle="modal"><span class="glyphicon glyphicon-refresh"></span> Start Processing</a></p>
      </div>
    @endif
    @if(Session::get('unprocessed_plansheets') == true)
      <div class="alert alert-info" role="alert">
        <p>Your sheets are ready for publishing. <a href="/planroom/sheets/review" class="btn btn-info btn-sm btn-spacer-left"><span class="glyphicon glyphicon-eye-open"></span> Review & Publish</a></p>
      </div>
    @endif
    
    @if(count($sheets) > 0)
    <div class="row">
      <?php
        $s3 = App::make('aws')->get('s3');
        $last = '';
      ?>  
      @foreach($sheets AS $sheet)
        <?php
          $current = $sheet->sheet_discipline;
          if ($last != $current) {
            echo '<div class="sheet_discipline_heading">' . $current . '</div>';
            $last = $current;
          }
        ?>  
        <a href="/planroom/sheet/{!! $sheet->sheet_code !!}" title="{!! $sheet->sheet_number . ' ' . $sheet->sheet_name !!}">
          <div class="col-sm-5 col-md-4 col-lg-2">
            <div class="sheet_thumb_tile" onclick="load_module('engineering_planroom_sheet', {'sheet_id':'<?php echo $sheet->sheet_code; ?>');">
              <img class="sheet_thumb" src="{!! $s3->getObjectUrl('tiles.aecore.com', $sheet->sheet_code . '/1.png') !!}" />
              <div class="sheet_thumb_footer">
                <p class="sheet_number">
                  <span class="pull-right small">&#9651;{!! $sheet->sheet_revision !!}</span>
                  <span class="bold">{!! $sheet->sheet_number !!}</span>
                </p>
                <p class="sheet_name">{!! $sheet->sheet_name !!}</p>
              </div>
            </div>
          </div>
        </a>
      @endforeach
    </div>
    @else
    <div class="alert alert-info no-margin">
      <p class="bold">No sheets were found.</p>
      <p><a href="planroom/modal/upload" class="btn btn-success btn-xs" data-target="#modal" data-toggle="modal"><span class="glyphicon glyphicon-cloud-upload"></span> Upload Plans</a> to get started.</p>
    </div>
    @endif
  </div>
</div>

@stop
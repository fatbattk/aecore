@extends('layouts.application.main_wide')
@extends('layouts.application.project_nav')
@section('content')

<script type="text/javascript" src="{!! URL::asset('js/planroom.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/panzoom.js') !!}"></script>
<script>
  $(document).ready(function(){
    // Setup sheet preview
    var $section = $('section').first();
    $section.find('.panzoom').panzoom();
    
    //Date selector
    $("#sheet_date").datepicker({
      changeMonth: true,
      changeYear: true
    }); 
  });
</script>

<div class="page-wrapper">
  <div class="pagehead">
    <div class="container-fluid">
      <span class="btn btn-primary pull-left toggle-nav" style="margin-right:10px;padding:7px;" onClick="$('#projectnav').toggle();"><span class="glyphicon glyphicon-menu-hamburger"></span></span>
      <h1>Plan Room / Review Sheets - {!! '(' . $sheetcount . ') Remaining' !!}</h1>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
    <div class="col-md-6 col-lg-5">
      <form class="form-horizontal form-wrapper" enctype="multipart/form-data" id="sheet_process" method="post" action="/planroom/sheets/publish">
        <h1 class="form-name">Sheet Information</h1>
        <div class="form-group">
          <label class="col-sm-3 control-label">Plan Set</label>
          <div class="col-sm-9 col-lg-7">
            <p class="form-control-static bold">{!! $sheet->set_name !!}</p>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="sheet_number">Sheet Number</label>
          <div class="col-sm-9 col-lg-7">
            <input type="text" class="form-control" id="sheet_number" name="sheet_number" placeholder="Ex: A6.0" value="{!! $sheet->sheet_number !!}" onBlur="define_discipline(this.value);update_revision(this.value);" autofocus required autocomplete="off"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="sheet_name">Sheet Name</label>
          <div class="col-sm-9 col-lg-7">
            <input type="text" class="form-control" id="sheet_name" name="sheet_name" placeholder="Ex: Exterior Elevations" value="{!! $sheet->sheet_name !!}" required autocomplete="off"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="sheet_date">Sheet Date</label>
          <div class="col-sm-9 col-lg-7">
            <input type="text" class="form-control" id="sheet_date" name="sheet_date" placeholder="Select date..." value="{!! date('m/d/Y', strtotime($sheet->set_date)) !!}" required/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="sheet_discipline">Discipline</label>
          <div class="col-sm-9 col-lg-7">
            <input type="text" class="form-control" id="sheet_discipline" name="sheet_discipline" placeholder="Ex: Structural" value="" required/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="sheet_revision">Revision</label>
          <div class="col-sm-9 col-lg-7">
            <input type="text" class="form-control" id="sheet_revision" name="sheet_revision" placeholder="Revision/Delta #..." value="{!! $sheet->sheet_revision !!}" required/>
          </div>
        </div>
        <div class="form-group no-margin">
          <div class="col-sm-offset-3 col-sm-9 col-lg-7">
            <input type="hidden" class="form-control" name="sheet_code" value="{!! $sheet->sheet_code !!}" required/>
            <button class="btn btn-success"><span class="glyphicon glyphicon-check"></span> Publish Sheet</button><span class="text-danger cursor btn-spacer-left"><span class="glyphicon glyphicon-trash"></span> Delete</span>
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-6 col-lg-4">
      <section>
        <div class="parent">
          <div class="panzoom">
            <?php $s3 = App::make('aws')->get('s3'); ?>
            <img src="{!! $s3->getObjectUrl('tiles.aecore.com', $sheet->sheet_code . '/2.png') !!}" />
          </div>
        </div>
      </section>
    </div>
  </div>
  </div>
</div>
@stop
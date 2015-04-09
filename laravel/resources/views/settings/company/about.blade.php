@extends('layouts.application.main')
@section('content')

  <script type="text/javascript" src="{!! URL::asset('js/countries.js') !!}"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      //Insert country & state
      print_country("country", "state", "{!! $companydata->country !!}", "{!! $companydata->state !!}");
    });
  </script>

  <div class="pagehead">
    <div class="container">
      <h1>Settings / Company / About</h1>
    </div>
  </div>
      
  <div class="container">
    <div class="row">
      @include('settings.nav')
      <div class="col-sm-8 col-md-9">
        
        @if(Session::has('UpdateSuccess'))
          <script type="text/javascript" charset="utf-8">
            setTimeout(function() {
              $("#deletesuccess").fadeOut("slow");
            }, 2500);
          </script>
          <div class="alert alert-success" id="deletesuccess"><span class="glyphicon glyphicon-ok"></span> {!! Session::get('UpdateSuccess') !!}</div>
        @endif

        <div class="panel panel-default">
          <div class="panel-heading">About {!! Session::get('company_name') !!}</div>
          <div class="panel-body">
            
          {!! Form::open(array('url' => '/settings/avatar/upload/company', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true)) !!}
            <div class="form-group">
              {!! Form::label('avatar', 'Avatar', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                <img src="{!! $avatar_url !!}" class="avatar_lg" />
                <div class="avatar_upload">
                  <script type="text/javascript">
                    <?php $timestamp = time();?>
                    $(function() {
                      $('#avatar').uploadifive({
                        'buttonText'  : 'Select File',
                        'multi'       : false,
                        'uploadLimit' : 1,
                        'width'       : 95,
                        'height'      : 34,
                        'buttonCursor' : 'pointer',
                        'fileType'    : 'image/*',
                        'formData'    : {
                          'timestamp' : '<?php echo $timestamp;?>',
                          'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
                        },
                        'queueID'           : 'queue',
                        'uploadScript'      : '/settings/avatar/upload/company',
                        'onAddQueueItem' : function(file){
                          var fileName = file.name;
                          var ext = fileName.substring(fileName.lastIndexOf('.') + 1); // Extract EXT
                          switch (ext) {
                            case 'png':
                            case 'PNG':
                            case 'jpg':
                            case 'JPG':
                              //do nothing
                            break;
                            default:
                              alert('Filetype not accepted, .png & .jpg only.');
                              $('#avatar').uploadifive('cancel', file);
                              break;
                            }
                          },
                        'onUploadComplete'  : function(file, data) {
                          console.log(data);
                          $('#modal').modal({
                            remote: '/settings/avatar/crop/company'
                          });
                        }
                      });
                    });
                  </script>
                  {!! Form::file("file", ["id" => "avatar"]) !!}
                  <div id="queue" class="queue"><span class="text-muted small">Or drag & drop image here.</span></div>
                </div>
              </div>
            </div>
            {!! Form::close() !!}
            
            {!! Form::open(array('id'=>'logo_form', 'url' => 'settings/logo/upload/complete', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true)) !!}
            <div class="form-group">
              {!! Form::label('logo', 'Logo', array('class' => 'col-md-3 col-lg-2 control-label')) !!}   
              <div class="col-md-9 col-lg-8">
                <img src="{!! $logo_url !!}" class="logo_company" />
                <script type="text/javascript">
                  <?php $timestamp = time();?>
                  $(function() {
                    $('#logo').uploadifive({
                      'buttonText'  : 'Select File',
                      'multi'       : false,
                      'uploadLimit' : 1,
                      'width'       : 95,
                      'height'      : 34,
                      'buttonCursor' : 'pointer',
                      'fileType'    : 'image/*',
                      'formData'    : {
                        'timestamp' : '<?php echo $timestamp;?>',
                        'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
                      },
                      'queueID'           : 'queue_logo',
                      'uploadScript'      : '/settings/logo/upload',
                      'onAddQueueItem' : function(file){
                        var fileName = file.name;
                        var ext = fileName.substring(fileName.lastIndexOf('.') + 1); // Extract EXT
                        switch (ext) {
                          case 'png':
                          case 'PNG':
                          case 'jpg':
                          case 'JPG':
                            //do nothing
                          break;
                          default:
                            alert('Filetype not accepted, .png & .jpg only.');
                            $('#logo').uploadifive('cancel', file);
                            break;
                          }
                        },
                      'onUploadComplete'  : function(file, data) {
                        console.log(data);
                        $("#logo_id_list").append('<input type="hidden" id="file_id_' + data + '" name="file_id[]" value="' + data + '"/>');
                        $("#logo_form").submit();
                      }
                    });
                  });
                </script>
                {!! Form::file("file", ["id" => "logo"]) !!}
                <div id="queue_logo" class="queue"><span class="text-muted small">Or drag & drop image here.</span></div>
                <div id="logo_id_list"></div>
              </div>
            </div>
            {!! Form::close() !!}
            
            {!! Form::open(array('url' => 'settings/company/update', 'method' => 'post', 'class' => 'form-horizontal')) !!}
              <div class="form-group">
                {!! Form::label('name', 'Company', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::text('name', $companydata->name, array('class' => 'form-control', 'placeholder' => 'Company', 'required'=>'true' )) !!}
                  <span class="text-danger">{!! $errors->first('name') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('type', 'Type', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::select('type', array(
                      '' => 'Select Company Type',
                      'Architect' => 'Architect',
                      'Broker' => 'Broker',
                      'Construction Manager' => 'Construction Manager',
                      'Consultant' => 'Consultant',
                      'Engineer' => 'Engineer',
                      'General Contractor' => 'General Contractor',
                      'Lender' => 'Lender',
                      'Municipality' => 'Municipality',
                      'Owner' => 'Owner',
                      'Subcontractor' => 'Subcontractor',
                      'Vendor' => 'Vendor'
                    ), $companydata->type, array('class'=>'form-control', 'required'=>'true'))
                  !!}
                  <span class="text-danger">{!! $errors->first('type') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('labor', 'Labor', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::select('labor', array(
                      'Not Applicable' => 'Not Applicable',
                      'Non-union' => 'Non-union',
                      'Union' => 'Union'
                    ), $companydata->labor, array('class' => 'form-control'))
                  !!}
                  <span class="text-danger">{!! $errors->first('labor') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('street', 'Street', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::text('street', $companydata->street, array('class' => 'form-control', 'placeholder' => 'Street', 'required'=>'true' )) !!}
                  <span class="text-danger">{!! $errors->first('street') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('city', 'City', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::text('city', $companydata->city, array('class' => 'form-control', 'placeholder' => 'City', 'required'=>'true' )) !!}
                  <span class="text-danger">{!! $errors->first('city') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('country', 'Country', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::select('country', array(
                      '' => 'Select Country'
                    ), null, array('class' => 'form-control', 'onChange' => 'print_state(\'state\', this.selectedIndex)'))
                  !!}
                  <span class="text-danger">{!! $errors->first('country') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('state', 'State', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::select('state', array(
                      '' => 'Select State'
                    ), null, array('class' => 'form-control'))
                  !!}
                  <span class="text-danger">{!! $errors->first('state') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('zipcode', 'Zip Code', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::text('zipcode', $companydata->zipcode, array('class' => 'form-control', 'placeholder' => 'Zip Code', 'required'=>'true' )) !!}
                  <span class="text-danger">{!! $errors->first('zipcode') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('main', 'Phone', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::text('main', $companyphone->number, array('class' => 'form-control', 'placeholder' => 'Phone Number' )) !!}
                  <span class="text-danger">{!! $errors->first('main') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('fax', 'Fax', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::text('fax', $companyfax->number, array('class' => 'form-control', 'placeholder' => 'Fax Number' )) !!}
                  <span class="text-danger">{!! $errors->first('fax') !!}</span>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('website', 'Website', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
                <div class="col-md-9 col-lg-8">
                  {!! Form::text('website', $companydata->url, array('class' => 'form-control', 'placeholder' => 'http://www.aecore.com' )) !!}
                  <span class="text-danger">{!! $errors->first('website') !!}</span>
                </div>
              </div>

              <div class="form-group no-margin">
                <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
                  {!! Form::submit('Update Company', array('class' => 'btn btn-success')) !!}
                  <span class="text-muted small pull-right" style="margin-top:10px;">Last updated {!! Timezone::convertFromUTC($companydata->updated_at, Auth::user()->timezone, 'Y-m-d h:ia') !!}</span>
                </div>
              </div>
            {!! Form::close() !!}
          </div>
        </div>
        
      </div>
    </div>
  </div>
@stop
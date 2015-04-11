@extends('layouts.application.main')
@section('content')

  <div class="pagehead">
    <div class="container">
      <h1>Settings / Profile</h1>
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
          <div class="panel-heading">Profile</div>
          <div class="panel-body">
            {!! Form::open(array('url' => '/settings/avatar/upload/profile', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true)) !!}
            <div class="form-group">
              {!! Form::label('name', 'Avatar', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                <img src="{!! Auth::user()->gravatar !!}" class="avatar_lg" title="{!! Auth::user()->name !!}" />
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
                        'uploadScript'      : '/settings/avatar/upload/profile',
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
                            remote: '/settings/avatar/crop/profile'
                          });
                        }
                      });
                    });
                  </script>
                  {!! Form::file("file", ["id" => "avatar"]) !!}
                  <div id="queue" class="queue"><span class="text-muted small">Or drag & drop image here.</span></div>
                  <div id="file_id_list"></div>
                </div>
              </div>
            </div>
            {!! Form::close() !!}
            
            {!! Form::open(array('url' => 'settings/profile', 'method' => 'post', 'class' => 'form-horizontal')) !!}
            <div class="form-group">
              {!! Form::label('name', 'Full Name', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::text('name', Auth::user()->name, array('class' => 'form-control', 'placeholder' => 'Full Name' )) !!}
                <span class="text-danger">{!! $errors->first('name') !!}</span>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('username', 'Username', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::text('username', Auth::user()->username, array('class' => 'form-control', 'placeholder' => 'Username' )) !!}
                <span class="text-danger">{!! $errors->first('username') !!}</span>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('title', 'Title', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::text('title', Auth::user()->title, array('class' => 'form-control', 'placeholder' => 'Title' )) !!}
                <span class="text-danger">{!! $errors->first('title') !!}</span>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('mobile', 'Mobile #', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::text('mobile', @Auth::user()->userphone->mobile, array('class' => 'form-control', 'placeholder' => '(xxx) xxx-xxxx')) !!}
                <span class="text-danger">{!! $errors->first('mobile') !!}</span>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('direct', 'Direct #', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Form::text('direct', @Auth::user()->userphone->direct, array('class' => 'form-control', 'placeholder' => '(xxx) xxx-xxxx')) !!}
                <span class="text-danger">{!! $errors->first('direct') !!}</span>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('timezone', 'Timezone', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
              <div class="col-md-9 col-lg-8">
                {!! Timezone::selectForm(Auth::user()->timezone, 'Select a Timezone', array('name' => 'timezone', 'class' => 'form-control')) !!}
                <span class="text-danger">{!! $errors->first('timezone') !!}</span>
              </div>
            </div>

            <div class="form-group no-margin">
              <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
                {!! Form::submit('Update Profile', array('class' => 'btn btn-success')) !!}
                <span class="text-muted small pull-right" style="margin-top:10px;">Last updated {!! Timezone::convertFromUTC(Auth::user()->updated_at, Auth::user()->timezone, 'Y-m-d h:ia') !!}</span>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
        
      </div>
    </div>
  </div>
@stop
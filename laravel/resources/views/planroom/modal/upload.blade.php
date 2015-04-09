<script type="text/javascript">
   $(document).ready(function(){
      //Date selector
      $("#set_date").datepicker({
         changeMonth: true,
         changeYear: true
      });
      
      $('#addtoset').change(function(){
        if($(this).is(':checked')){
          $('#existing_set').show();
          $('#new_set').hide();
          $('#set_name').removeAttr('required');
          $('#set_date').removeAttr('required');
        } else {
          $('#existing_set').hide();
          $('#new_set').show();
          $('#set_name').attr('required', 'true');
          $('#set_date').attr('required', 'true');
        }
      });
   });
</script>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title">Upload Plans</h4>
</div>
{!! Form::open(array('url' => 'planroom/upload', 'method' => 'post', 'class' => 'form-horizontal no-margin', 'files'=>true)) !!}
  <div class="modal-body">
    
    @if(count($sets) > 0)
      <div class="checkbox" style="margin:0 0 10px 0;">
        <div class="col-sm-7 col-sm-offset-3">
          <label>
            {!! Form::checkbox('addtoset', 'yes', null, ['id'=>'addtoset']) !!} Add to an existing set
          </label>
        </div>
      </div>

      <div class="form-group" id="existing_set" style="display:none;">
        {!! Form::label('set_code', 'Existing Sets', array('class' => 'col-sm-3 control-label')) !!}
        <div class="col-sm-7">
          <select name="set_code" class="form-control">
            @foreach($sets AS $set)
              <option value="{!! $set->set_code !!}">{!! date('Y-m-d', strtotime($set->set_date)) . ' ' . $set->set_name !!}</option>
            @endforeach
          </select>
          <span class="text-danger">{!! $errors->first('status') !!}</span>
        </div>
      </div>
    @endif
    
    <div id="new_set">
      <div class="form-group">
        {!! Form::label('set_name', 'Set Name', array('class' => 'col-sm-3 control-label')) !!}
        <div class="col-sm-7">
          {!! Form::text('set_name', null, array('id'=>'set_name', 'class'=>'form-control', 'placeholder'=>'Ex. Issued for Permit', 'required'=>'true', 'autofocus'=>'true')) !!}
        </div>
      </div>
      <div class="form-group">
        {!! Form::label('set_date', 'Set Date', array('class' => 'col-sm-3 control-label')) !!}
        <div class="col-sm-4">
          {!! Form::text('set_date', null, array('id'=>'set_date', 'class'=>'form-control', 'placeholder'=>'Set Date...', 'required'=>'true')) !!}
        </div>
      </div>
    </div>
    
    <div class="form-group no-margin">
      {!! Form::label('file', 'Upload', array('class' => 'col-sm-3 control-label')) !!}
      <div class="col-sm-7">
        <div class="file_upload">
          <script type="text/javascript">
            <?php $timestamp = time();?>
            $(function() {
              $('#file').uploadifive({
                'buttonText' : 'Select Plans',
                'multi' : true,
                'formData' : {
                  'timestamp' : '<?php echo $timestamp;?>',
                  'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
                },
                'queueID'           : 'queue',
                'uploadScript'      : '/attachment/upload',
                'onAddQueueItem' : function(file){
                  var fileName = file.name;
                  var ext = fileName.substring(fileName.lastIndexOf('.') + 1); // Extract EXT
                  switch (ext) {
                    case 'pdf':
                    case 'PDF':
                      //do nothing
                    break;
                    default:
                      alert('Filetype not accepted, .pdf only.');
                      $('#file').uploadifive('cancel', file);
                      break;
                    }
                  },                
                'onUploadComplete'  : function(file, data) {
                  console.log(data);
                  $('.close').remove();
                  $("#file_id_list").append('<input type="hidden" id="file_id_' + data + '" name="file_id[]" value="' + data + '"/>');
                  $('#upload_button').show();
                }
              });
            });
          </script>
          {!! Form::file("file", ["id" => "file"]) !!}
          <div id="queue" class="queue"><span class="text-muted small">Or drag & drop PDF files here.</span></div>
          <div id="file_id_list"></div>
        </div>
      </div>
    </div>    
  </div>
  <div class="modal-footer" style="margin:0;">
    <button type="submit" id="upload_button" style="display:none;" class="btn btn-success" title="Upload and process your plans."><span class="glyphicon glyphicon-cloud-upload"></span> Upload</button>
    <button type="button" class="btn btn-default btn-spacer-left" data-dismiss="modal">Cancel</button>
  </div>
{!! Form::close() !!}
<script type="text/javascript" src="{!! URL::asset('js/jcrop/jquery.Jcrop.js') !!}"></script>
<script type="text/javascript">
  $(function(){
    $('#jcrop_target').Jcrop({
      onChange: updateCoords,
      onSelect: updateCoords,
      aspectRatio: 1,
      setSelect: [ 120, 85, 420, 420 ]
    });
    function updateCoords(c){
      $('#x').val(c.x);
      $('#y').val(c.y);
      $('#w').val(c.w);
      $('#h').val(c.h);
    };
  });
</script>


{!! Form::open(array('url' => '/settings/avatar/crop/' . $type, 'method' => 'post', 'class' => 'form-horizontal')) !!}
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><span class="glyphicon glyphicon-picture"></span> Crop Avatar</h4>
  </div>

  <div class="modal-body">
    @if($type == 'profile')
      {!! HTML::image('uploads/' . Auth::User()->id . '-' . $type . '-avatar-original.jpg', '', array('id'=>'jcrop_target', 'style'=>'width:100%;')); !!}
    @elseif($type == 'company')
      {!! HTML::image('uploads/' . Auth::User()->company['id'] . '-' . $type . '-avatar-original.jpg', '', array('id'=>'jcrop_target', 'style'=>'width:100%;')); !!}
    @endif
    <input type="hidden" id="x" name="x" value="" required />
    <input type="hidden" id="y" name="y" value="" required />
    <input type="hidden" id="w" name="w" value="" required />
    <input type="hidden" id="h" name="h" value="" required />
  </div>

  <div class="modal-footer" style="margin:0;">
    <button type="submit" class="btn btn-success" title="Crop & save avatar picture.">Save</button>
    <button type="button" class="btn btn-default btn-spacer-left" data-dismiss="modal" title="Cancel & close modal window.">Cancel</button>
  </div>
{!! Form::close() !!}
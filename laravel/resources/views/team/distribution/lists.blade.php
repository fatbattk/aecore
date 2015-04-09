 <script type="text/javascript">
   $(document).ready(function() {
      $('#list_name').keypress(function(e){
         if(e.which == 13){
            addDistributionList('<?php echo Session::get('project_code'); ?>');
         }
      });
   });
</script>

<div style="background:#F7F7F7;padding:7px 10px;line-height:22px;border-bottom:1px solid #ddd;">
    <span class="btn btn-xs btn-success pull-right" onclick="$('#addlist').show();$('#list_name').focus();"><span class="glyphicon glyphicon-plus"></span> Add</span>
    <span class="text-muted small bold">Distribution Lists</span>
</div>
<div class="distributeTile" id="addlist" style="display:none;">
  <div class="form-group no-margin">
    {!! Form::text('list_name', null, array('id'=>'list_name', 'class' => 'form-control', 'placeholder' => 'Add distribution list...', 'autocomplete'=>'off' )) !!}
  </div>
</div>
@if(count($lists) > 0)
  @foreach($lists AS $list)
    <div class="distributeTile pointer" id="list_{!! $list->code !!}" onClick="showDistributionList('<?php echo $list->code; ?>');$('.distributeTile').removeClass('active');$(this).addClass('active');">
      {!! $list->list_name !!}
    </div>
  @endforeach
@else
  <div class="alert alert-warning" style="margin:7px;">
    <p class="bold">No lists found!</p>
    <p style="margin:0;"><span class="btn-link" onClick="$('#addlist').show();$('#list_name').focus();">Create a List</span> to get started.</p>
  </div>
@endif